<?php
/**
 * Sync Gateway Data Cron Job.
 *
 * Periodically fetches account balance, sender IDs, and coverage data
 * from the kwtSMS API and caches it in the kwtsms_gateway_data table.
 *
 * @see \KwtSms\SmsIntegration\Model\Api\Client
 * @see \KwtSms\SmsIntegration\Model\ResourceModel\GatewayData
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Cron;

use KwtSms\SmsIntegration\Api\Data\GatewayDataInterface;
use KwtSms\SmsIntegration\Model\Api\Client;
use KwtSms\SmsIntegration\Model\Config;
use KwtSms\SmsIntegration\Model\GatewayDataFactory;
use KwtSms\SmsIntegration\Model\ResourceModel\GatewayData as GatewayDataResource;
use Psr\Log\LoggerInterface;

class SyncGatewayData
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var Client
     */
    private Client $apiClient;

    /**
     * @var GatewayDataResource
     */
    private GatewayDataResource $gatewayDataResource;

    /**
     * @var GatewayDataFactory
     */
    private GatewayDataFactory $gatewayDataFactory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Config $config
     * @param Client $apiClient
     * @param GatewayDataResource $gatewayDataResource
     * @param GatewayDataFactory $gatewayDataFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        Client $apiClient,
        GatewayDataResource $gatewayDataResource,
        GatewayDataFactory $gatewayDataFactory,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->apiClient = $apiClient;
        $this->gatewayDataResource = $gatewayDataResource;
        $this->gatewayDataFactory = $gatewayDataFactory;
        $this->logger = $logger;
    }

    /**
     * Sync gateway data from the kwtSMS API.
     *
     * Fetches balance, sender IDs, and coverage data, then persists
     * each result in the gateway_data table for local caching.
     *
     * @return void
     */
    public function execute(): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        if (!$this->config->getApiUsername() || !$this->config->getApiPassword()) {
            return;
        }

        try {
            $balance = $this->apiClient->getBalance();
            $this->saveGatewayData(GatewayDataInterface::TYPE_BALANCE, $balance);
        } catch (\Exception $e) {
            $this->logger->error('kwtSMS: Failed to sync balance', [
                'message' => $e->getMessage(),
            ]);
        }

        try {
            $senderIds = $this->apiClient->getSenderIds();
            $this->saveGatewayData(GatewayDataInterface::TYPE_SENDERID, $senderIds);
        } catch (\Exception $e) {
            $this->logger->error('kwtSMS: Failed to sync sender IDs', [
                'message' => $e->getMessage(),
            ]);
        }

        try {
            $coverage = $this->apiClient->getCoverage();
            $this->saveGatewayData(GatewayDataInterface::TYPE_COVERAGE, $coverage);
        } catch (\Exception $e) {
            $this->logger->error('kwtSMS: Failed to sync coverage data', [
                'message' => $e->getMessage(),
            ]);
        }

        $this->logger->info('kwtSMS: Gateway data synced successfully');
    }

    /**
     * Save or update a gateway data row by data type.
     *
     * If a row with the given type already exists, it is updated in place.
     * Otherwise a new row is created.
     *
     * @param string $type
     * @param mixed $value
     * @return void
     */
    private function saveGatewayData(string $type, $value): void
    {
        try {
            $connection = $this->gatewayDataResource->getConnection();
            $tableName = $this->gatewayDataResource->getMainTable();
            $now = date('Y-m-d H:i:s');
            $encodedValue = json_encode($value);

            // Check if a row with this type already exists
            $select = $connection->select()
                ->from($tableName, [GatewayDataInterface::ENTITY_ID])
                ->where(GatewayDataInterface::DATA_TYPE . ' = ?', $type)
                ->limit(1);

            $existingId = $connection->fetchOne($select);

            if ($existingId !== false) {
                $connection->update(
                    $tableName,
                    [
                        GatewayDataInterface::DATA_VALUE => $encodedValue,
                        GatewayDataInterface::SYNCED_AT  => $now,
                    ],
                    [GatewayDataInterface::DATA_TYPE . ' = ?' => $type]
                );
            } else {
                $connection->insert($tableName, [
                    GatewayDataInterface::DATA_TYPE  => $type,
                    GatewayDataInterface::DATA_VALUE => $encodedValue,
                    GatewayDataInterface::SYNCED_AT  => $now,
                ]);
            }
        } catch (\Exception $e) {
            $this->logger->error('kwtSMS: Failed to save gateway data', [
                'type'    => $type,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
