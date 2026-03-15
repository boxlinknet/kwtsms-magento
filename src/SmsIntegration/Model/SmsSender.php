<?php
/**
 * SMS Sender: single send() handles everything.
 *
 * Flow:
 * 1. Pre-flight: module enabled, gateway configured, sender ID set
 * 2. Clean message
 * 3. Normalize, deduplicate, filter by coverage
 * 4. Check cached balance once (before any API call)
 * 5. If <= 200 numbers: single API call
 *    If > 200 numbers: _bulkSend() splits into 200-number chunks with 0.5s delay
 * 6. On any successful send: update local balance from API balance-after field
 * 7. Log every outcome
 *
 * No balance re-checks during or after sending.
 * No unnecessary API calls.
 *
 * @see \KwtSms\SmsIntegration\Model\Api\Client
 * @see \KwtSms\SmsIntegration\Model\Phone\Normalizer
 * @see \KwtSms\SmsIntegration\Model\MessageCleaner
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model;

use KwtSms\SmsIntegration\Api\Data\GatewayDataInterface;
use KwtSms\SmsIntegration\Api\Data\SmsLogInterface;
use KwtSms\SmsIntegration\Api\Data\SmsLogInterfaceFactory;
use KwtSms\SmsIntegration\Api\SmsLogRepositoryInterface;
use KwtSms\SmsIntegration\Model\Api\Client;
use KwtSms\SmsIntegration\Model\Api\ErrorCodes;
use KwtSms\SmsIntegration\Model\Phone\Normalizer;
use KwtSms\SmsIntegration\Model\ResourceModel\GatewayData as GatewayDataResource;
use Psr\Log\LoggerInterface;

class SmsSender
{
    /** kwtSMS API limit: max recipients per request. */
    private const BATCH_SIZE = 200;

    /** Delay between bulk chunks in microseconds (0.5 seconds). */
    private const BATCH_DELAY_US = 500000;

    private Config $config;
    private Normalizer $normalizer;
    private MessageCleaner $messageCleaner;
    private Client $apiClient;
    private SmsLogRepositoryInterface $smsLogRepository;
    private SmsLogInterfaceFactory $smsLogFactory;
    private GatewayDataResource $gatewayDataResource;
    private LoggerInterface $logger;

    public function __construct(
        Config $config,
        Normalizer $normalizer,
        MessageCleaner $messageCleaner,
        Client $apiClient,
        SmsLogRepositoryInterface $smsLogRepository,
        SmsLogInterfaceFactory $smsLogFactory,
        GatewayDataResource $gatewayDataResource,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->normalizer = $normalizer;
        $this->messageCleaner = $messageCleaner;
        $this->apiClient = $apiClient;
        $this->smsLogRepository = $smsLogRepository;
        $this->smsLogFactory = $smsLogFactory;
        $this->gatewayDataResource = $gatewayDataResource;
        $this->logger = $logger;
    }

    /**
     * Send SMS to one or many recipients.
     *
     * @param string|array $recipients  Single phone string or array of phone strings
     * @param string       $message     Raw message (cleaned automatically)
     * @param string       $eventType   Event identifier for logging
     * @param string|null  $relatedEntityId   Order ID, customer ID, etc.
     * @param string|null  $relatedEntityType 'order', 'customer', etc.
     * @return array{success:bool, sent:int, failed:int, skipped:int, invalid:int, error:?string, msg_ids:string[]}
     */
    public function send(
        $recipients,
        string $message,
        string $eventType,
        ?string $relatedEntityId = null,
        ?string $relatedEntityType = null
    ): array {
        $result = [
            'success' => false,
            'sent'    => 0,
            'failed'  => 0,
            'skipped' => 0,
            'invalid' => 0,
            'error'   => null,
            'msg_ids' => [],
        ];

        // ---- 1. Pre-flight checks ----

        if (!$this->config->isEnabled()) {
            $result['error'] = 'SMS module is disabled. Enable it in Stores > Configuration > kwtSMS.';
            $this->logger->info('kwtSMS: ' . $result['error']);
            return $result;
        }

        if (!$this->config->getApiUsername() || !$this->config->getApiPassword()) {
            $result['error'] = 'Gateway not configured. Enter API credentials and click Login.';
            $this->logger->error('kwtSMS: ' . $result['error']);
            return $result;
        }

        $senderId = $this->config->getSenderId();
        if (!$senderId) {
            $result['error'] = 'No Sender ID selected. Login to the gateway first, then select a Sender ID.';
            $this->logger->error('kwtSMS: ' . $result['error']);
            return $result;
        }

        $testMode = $this->config->isTestMode();

        // ---- 2. Clean message ----

        $cleanMessage = $this->messageCleaner->clean($message);
        if ($cleanMessage === '') {
            $result['error'] = 'Message is empty after cleaning (emoji, HTML, hidden characters removed).';
            $this->logger->error('kwtSMS: ' . $result['error']);
            return $result;
        }

        // ---- 3. Normalize, deduplicate, coverage filter ----

        $inputNumbers = is_array($recipients) ? $recipients : [$recipients];
        if (empty($inputNumbers)) {
            $result['error'] = 'No recipients provided.';
            return $result;
        }

        $defaultCountry = $this->config->getDefaultCountryCode();
        $validNumbers = [];
        $seen = [];

        foreach ($inputNumbers as $raw) {
            $raw = trim((string) $raw);
            if ($raw === '') {
                $result['invalid']++;
                continue;
            }

            $phoneResult = $this->normalizer->normalize($raw, $defaultCountry);
            if (!$phoneResult->isValid()) {
                $result['invalid']++;
                $this->saveLog(
                    $raw, $cleanMessage, $eventType, SmsLogInterface::STATUS_FAILED,
                    null, null, 'Invalid number: ' . $phoneResult->getError(),
                    $relatedEntityId, $relatedEntityType, $testMode
                );
                continue;
            }

            $normalized = $phoneResult->getNormalized();

            // Deduplicate
            if (isset($seen[$normalized])) {
                continue;
            }
            $seen[$normalized] = true;
            $validNumbers[] = $normalized;
        }

        if (empty($validNumbers)) {
            $result['error'] = 'No valid phone numbers after normalization.';
            return $result;
        }

        // Coverage filter
        $coveragePrefixes = $this->loadCoveragePrefixes();
        if (!empty($coveragePrefixes)) {
            $covered = [];
            foreach ($validNumbers as $number) {
                if ($this->normalizer->isInCoverage($number, $coveragePrefixes)) {
                    $covered[] = $number;
                } else {
                    $result['skipped']++;
                    $cc = $this->normalizer->findCountryCode($number);
                    $this->saveLog(
                        $number, $cleanMessage, $eventType, SmsLogInterface::STATUS_SKIPPED,
                        null, null,
                        'Country +' . ($cc ?? '?') . ' not in your coverage.',
                        $relatedEntityId, $relatedEntityType, $testMode
                    );
                }
            }
            $validNumbers = $covered;
        }

        if (empty($validNumbers)) {
            $result['error'] = 'All numbers filtered out. Recipient countries not active on your account.';
            return $result;
        }

        // ---- 4. Balance check (once, before any API call) ----

        $balance = $this->getAvailableBalance();
        if ($balance !== null && $balance <= 0) {
            $result['error'] = 'Account balance is zero. Recharge at kwtsms.com before sending.';
            $this->logger->warning('kwtSMS: ' . $result['error']);
            foreach ($validNumbers as $number) {
                $result['skipped']++;
                $this->saveLog(
                    $number, $cleanMessage, $eventType, SmsLogInterface::STATUS_SKIPPED,
                    null, null, 'Account balance is zero.',
                    $relatedEntityId, $relatedEntityType, $testMode
                );
            }
            return $result;
        }

        // ---- 5. Send ----

        $count = count($validNumbers);

        if ($count <= self::BATCH_SIZE) {
            // Single API call (1-200 numbers)
            $mobileParam = implode(',', $validNumbers);
            $sendResult = $this->callApi($senderId, $mobileParam, $cleanMessage, $testMode,
                $eventType, $relatedEntityId, $relatedEntityType);

            $result['sent'] = $sendResult['sent'];
            $result['failed'] = $sendResult['failed'];
            if ($sendResult['msg_id']) {
                $result['msg_ids'][] = $sendResult['msg_id'];
            }
        } else {
            // Bulk: > 200 numbers, split and send with delay
            $bulkResult = $this->_bulkSend($validNumbers, $senderId, $cleanMessage, $testMode,
                $eventType, $relatedEntityId, $relatedEntityType);

            $result['sent'] = $bulkResult['sent'];
            $result['failed'] = $bulkResult['failed'];
            $result['msg_ids'] = $bulkResult['msg_ids'];
        }

        $result['success'] = $result['sent'] > 0;
        return $result;
    }

    /**
     * Bulk send for > 200 numbers. Called only from send().
     * Splits into 200-number chunks with 0.5 second delay between each.
     */
    private function _bulkSend(
        array $numbers,
        string $senderId,
        string $cleanMessage,
        bool $testMode,
        string $eventType,
        ?string $relatedEntityId,
        ?string $relatedEntityType
    ): array {
        $totals = ['sent' => 0, 'failed' => 0, 'msg_ids' => []];
        $chunks = array_chunk($numbers, self::BATCH_SIZE);

        foreach ($chunks as $index => $chunk) {
            if ($index > 0) {
                usleep(self::BATCH_DELAY_US);
            }

            $mobileParam = implode(',', $chunk);
            $sendResult = $this->callApi($senderId, $mobileParam, $cleanMessage, $testMode,
                $eventType, $relatedEntityId, $relatedEntityType);

            $totals['sent'] += $sendResult['sent'];
            $totals['failed'] += $sendResult['failed'];
            if ($sendResult['msg_id']) {
                $totals['msg_ids'][] = $sendResult['msg_id'];
            }
        }

        return $totals;
    }

    /**
     * Make one API send call and log the result.
     * On success, updates local balance from the API balance-after field.
     *
     * @return array{sent:int, failed:int, msg_id:?string}
     */
    private function callApi(
        string $senderId,
        string $mobileParam,
        string $cleanMessage,
        bool $testMode,
        string $eventType,
        ?string $relatedEntityId,
        ?string $relatedEntityType
    ): array {
        $numberCount = substr_count($mobileParam, ',') + 1;

        try {
            $response = $this->apiClient->sendSms($senderId, $mobileParam, $cleanMessage, $testMode);

            if (isset($response['result']) && $response['result'] === 'OK') {
                $status = $testMode ? SmsLogInterface::STATUS_TEST : SmsLogInterface::STATUS_SENT;
                $msgId = $response['msg-id'] ?? null;
                $charged = isset($response['points-charged']) ? (int) $response['points-charged'] : null;

                $this->saveLog(
                    $mobileParam, $cleanMessage, $eventType, $status,
                    $msgId, $charged, null,
                    $relatedEntityId, $relatedEntityType, $testMode,
                    json_encode($response)
                );

                // Update local balance from API response (only source of truth after send)
                if (isset($response['balance-after'])) {
                    $this->updateLocalBalance((string) $response['balance-after']);
                }

                return ['sent' => $numberCount, 'failed' => 0, 'msg_id' => $msgId];
            }

            // API returned an error
            $errorCode = $response['code'] ?? $response['result'] ?? 'UNKNOWN';
            $errorDesc = $response['description'] ?? ErrorCodes::getDescription($errorCode);

            $this->logger->error('kwtSMS: API error', ['code' => $errorCode, 'desc' => $errorDesc]);
            $this->saveLog(
                $mobileParam, $cleanMessage, $eventType, SmsLogInterface::STATUS_FAILED,
                null, null, $errorCode . ': ' . $errorDesc,
                $relatedEntityId, $relatedEntityType, $testMode,
                json_encode($response)
            );

            return ['sent' => 0, 'failed' => $numberCount, 'msg_id' => null];
        } catch (\Exception $e) {
            $this->logger->error('kwtSMS: Send failed', ['message' => $e->getMessage()]);
            $this->saveLog(
                $mobileParam, $cleanMessage, $eventType, SmsLogInterface::STATUS_FAILED,
                null, null, 'System error: ' . $e->getMessage(),
                $relatedEntityId, $relatedEntityType, $testMode
            );

            return ['sent' => 0, 'failed' => $numberCount, 'msg_id' => null];
        }
    }

    // -------------------------------------------------------------------------
    // Helpers (all private, no extra API calls)
    // -------------------------------------------------------------------------

    /** Read available balance from local cache. No API call. */
    private function getAvailableBalance(): ?float
    {
        $raw = $this->getGatewayValue(GatewayDataInterface::TYPE_BALANCE);
        if ($raw === null) {
            return null;
        }
        $data = json_decode($raw, true);
        if (is_array($data) && isset($data['available'])) {
            return (float) $data['available'];
        }
        return (float) $raw;
    }

    /** Read coverage prefixes from local cache. No API call. */
    private function loadCoveragePrefixes(): array
    {
        $raw = $this->getGatewayValue(GatewayDataInterface::TYPE_COVERAGE);
        if ($raw === null) {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /** Persist an SMS log entry. */
    private function saveLog(
        string $recipient,
        string $message,
        string $eventType,
        string $status,
        ?string $msgId = null,
        ?int $pointsCharged = null,
        ?string $errorCode = null,
        ?string $relatedEntityId = null,
        ?string $relatedEntityType = null,
        bool $testMode = false,
        ?string $apiResponse = null
    ): void {
        try {
            $smsLog = $this->smsLogFactory->create();
            $smsLog->setRecipient($recipient);
            $smsLog->setMessage($message);
            $smsLog->setEventType($eventType);
            $smsLog->setStatus($status);
            $smsLog->setTestMode($testMode ? 1 : 0);
            if ($msgId !== null) {
                $smsLog->setMsgId($msgId);
            }
            if ($pointsCharged !== null) {
                $smsLog->setPointsCharged($pointsCharged);
            }
            if ($errorCode !== null) {
                $smsLog->setErrorCode(mb_substr($errorCode, 0, 255));
            }
            if ($relatedEntityId !== null) {
                $smsLog->setRelatedEntityId($relatedEntityId);
            }
            if ($relatedEntityType !== null) {
                $smsLog->setRelatedEntityType($relatedEntityType);
            }
            if ($apiResponse !== null) {
                $smsLog->setApiResponse($apiResponse);
            }
            $this->smsLogRepository->save($smsLog);
        } catch (\Exception $e) {
            $this->logger->error('kwtSMS: Failed to save log', ['message' => $e->getMessage()]);
        }
    }

    /** Read a value from kwtsms_gateway_data. DB only, no API call. */
    private function getGatewayValue(string $type): ?string
    {
        try {
            $connection = $this->gatewayDataResource->getConnection();
            $tableName = $this->gatewayDataResource->getMainTable();
            $select = $connection->select()
                ->from($tableName, [GatewayDataInterface::DATA_VALUE])
                ->where(GatewayDataInterface::DATA_TYPE . ' = ?', $type)
                ->limit(1);
            $value = $connection->fetchOne($select);
            return $value !== false ? (string) $value : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /** Update cached balance using the balance-after value from API send response. */
    private function updateLocalBalance(string $balanceAfter): void
    {
        try {
            $connection = $this->gatewayDataResource->getConnection();
            $tableName = $this->gatewayDataResource->getMainTable();
            $currentRaw = $this->getGatewayValue(GatewayDataInterface::TYPE_BALANCE);
            $data = ($currentRaw !== null) ? json_decode($currentRaw, true) : [];
            if (!is_array($data)) {
                $data = [];
            }
            $data['result'] = 'OK';
            $data['available'] = (int) $balanceAfter;
            $connection->update(
                $tableName,
                [
                    GatewayDataInterface::DATA_VALUE => json_encode($data),
                    GatewayDataInterface::SYNCED_AT  => date('Y-m-d H:i:s'),
                ],
                [GatewayDataInterface::DATA_TYPE . ' = ?' => GatewayDataInterface::TYPE_BALANCE]
            );
        } catch (\Exception $e) {
            $this->logger->error('kwtSMS: Failed to update balance cache', ['message' => $e->getMessage()]);
        }
    }
}
