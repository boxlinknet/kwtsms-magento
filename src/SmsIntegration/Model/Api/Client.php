<?php
/**
 * kwtSMS API Client.
 *
 * Handles all HTTP communication with the kwtSMS gateway.
 * Every request is an HTTPS POST with JSON body to https://www.kwtsms.com/API/.
 *
 * @see \KwtSms\SmsIntegration\Model\Config
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model\Api;

use KwtSms\SmsIntegration\Model\Config;
use Magento\Framework\HTTP\Client\CurlFactory;
use Psr\Log\LoggerInterface;

class Client
{
    /**
     * Base URL for the kwtSMS API.
     */
    private const BASE_URL = 'https://www.kwtsms.com/API';

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var CurlFactory
     */
    private CurlFactory $curlFactory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Config $config
     * @param CurlFactory $curlFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        CurlFactory $curlFactory,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->curlFactory = $curlFactory;
        $this->logger = $logger;
    }

    /**
     * Test the API connection with the given credentials.
     *
     * @param string $username
     * @param string $password
     * @return array
     */
    public function testConnection(string $username, string $password): array
    {
        return $this->doRequest('balance', [
            'username' => $username,
            'password' => $password,
        ]);
    }

    /**
     * Get the current account balance.
     *
     * @return array
     */
    public function getBalance(): array
    {
        $response = $this->doRequest('balance', [
            'username' => $this->config->getApiUsername(),
            'password' => $this->config->getApiPassword(),
        ]);

        return [
            'available' => $response['available'] ?? null,
            'purchased' => $response['purchased'] ?? null,
            'raw' => $response,
        ];
    }

    /**
     * Get the list of approved sender IDs.
     *
     * @return array
     */
    public function getSenderIds(): array
    {
        $response = $this->doRequest('senderid', [
            'username' => $this->config->getApiUsername(),
            'password' => $this->config->getApiPassword(),
        ]);

        return [
            'senderid' => $response['senderid'] ?? [],
            'raw' => $response,
        ];
    }

    /**
     * Get the coverage prefixes for the account.
     *
     * @return array
     */
    public function getCoverage(): array
    {
        $response = $this->doRequest('coverage', [
            'username' => $this->config->getApiUsername(),
            'password' => $this->config->getApiPassword(),
        ]);

        return [
            'prefixes' => $response['prefixes'] ?? [],
            'raw' => $response,
        ];
    }

    /**
     * Send an SMS message.
     *
     * @param string $sender
     * @param string $mobile
     * @param string $message
     * @param bool $testMode
     * @return array
     */
    public function sendSms(string $sender, string $mobile, string $message, bool $testMode = false): array
    {
        $params = [
            'username' => $this->config->getApiUsername(),
            'password' => $this->config->getApiPassword(),
            'sender'   => $sender,
            'mobile'   => $mobile,
            'message'  => $message,
        ];

        if ($testMode) {
            $params['test'] = 1;
        }

        $response = $this->doRequest('send', $params);

        return [
            'result'          => $response['result'] ?? null,
            'msg-id'          => $response['msg-id'] ?? null,
            'numbers'         => $response['numbers'] ?? null,
            'points-charged'  => $response['points-charged'] ?? null,
            'balance-after'   => $response['balance-after'] ?? null,
            'unix-timestamp'  => $response['unix-timestamp'] ?? null,
            'raw'             => $response,
        ];
    }

    /**
     * Validate phone numbers via the API.
     *
     * @param array $numbers
     * @return array
     */
    public function validateNumbers(array $numbers): array
    {
        return $this->doRequest('validate', [
            'username' => $this->config->getApiUsername(),
            'password' => $this->config->getApiPassword(),
            'mobile'   => implode(',', $numbers),
        ]);
    }

    /**
     * Execute an API request.
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     * @throws \RuntimeException
     */
    private function doRequest(string $endpoint, array $params): array
    {
        $url = self::BASE_URL . '/' . $endpoint . '/';

        try {
            $curl = $this->curlFactory->create();
            $curl->addHeader('Content-Type', 'application/json');
            $curl->addHeader('Accept', 'application/json');
            $curl->post($url, json_encode($params));

            $responseBody = $curl->getBody();
            $httpStatus = $curl->getStatus();

            if ($this->config->isDebugEnabled()) {
                // Never log credentials in debug output
                $safeParams = $params;
                unset($safeParams['password']);
                $this->logger->debug('kwtSMS API request', [
                    'endpoint' => $endpoint,
                    'params'   => $safeParams,
                    'status'   => $httpStatus,
                ]);
            }

            if ($httpStatus < 200 || $httpStatus >= 300) {
                $this->logger->error('kwtSMS API HTTP error', [
                    'endpoint' => $endpoint,
                    'status'   => $httpStatus,
                    'body'     => $responseBody,
                ]);
                throw new \RuntimeException(
                    sprintf('kwtSMS API returned HTTP %d for %s', $httpStatus, $endpoint)
                );
            }

            $decoded = json_decode($responseBody, true);
            if (!is_array($decoded)) {
                $this->logger->error('kwtSMS API invalid JSON response', [
                    'endpoint' => $endpoint,
                    'body'     => $responseBody,
                ]);
                throw new \RuntimeException(
                    sprintf('kwtSMS API returned invalid JSON for %s', $endpoint)
                );
            }

            if ($this->config->isDebugEnabled()) {
                $this->logger->debug('kwtSMS API response', [
                    'endpoint' => $endpoint,
                    'response' => $decoded,
                ]);
            }

            return $decoded;
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('kwtSMS API request failed', [
                'endpoint'  => $endpoint,
                'exception' => $e->getMessage(),
            ]);
            throw new \RuntimeException(
                sprintf('kwtSMS API request to %s failed: %s', $endpoint, $e->getMessage()),
                0,
                $e
            );
        }
    }
}
