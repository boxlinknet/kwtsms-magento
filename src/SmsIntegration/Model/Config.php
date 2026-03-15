<?php
/**
 * Configuration Helper.
 *
 * Reads all kwtSMS module settings from the Magento store configuration.
 * Handles credential decryption and provides typed accessors for each config path.
 *
 * @see \Magento\Framework\App\Config\ScopeConfigInterface
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'kwtsms/general/enabled';
    private const XML_PATH_TEST_MODE = 'kwtsms/general/test_mode';
    private const XML_PATH_DEBUG = 'kwtsms/general/debug';
    private const XML_PATH_DEFAULT_COUNTRY_CODE = 'kwtsms/general/default_country_code';
    private const XML_PATH_USERNAME = 'kwtsms/gateway/username';
    private const XML_PATH_PASSWORD = 'kwtsms/gateway/password';
    private const XML_PATH_SENDER_ID = 'kwtsms/general/sender_id';
    private const XML_PATH_ADMIN_PHONES = 'kwtsms/admin_alerts/phone_numbers';
    private const XML_PATH_INTEGRATIONS = 'kwtsms/integrations/';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    /**
     * Check if the module is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if test mode is active.
     *
     * @return bool
     */
    public function isTestMode(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_TEST_MODE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if debug logging is enabled.
     *
     * @return bool
     */
    public function isDebugEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DEBUG,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get the default country code for phone normalization.
     *
     * @return string
     */
    public function getDefaultCountryCode(): string
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_COUNTRY_CODE,
            ScopeInterface::SCOPE_STORE
        );

        return !empty($value) ? (string) $value : '965';
    }

    /**
     * Get the API username.
     *
     * @return string
     */
    public function getApiUsername(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_USERNAME,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get the decrypted API password.
     *
     * @return string
     */
    public function getApiPassword(): string
    {
        $value = (string) $this->scopeConfig->getValue(
            self::XML_PATH_PASSWORD,
            ScopeInterface::SCOPE_STORE
        );

        return $value !== '' ? $this->encryptor->decrypt($value) : '';
    }

    /**
     * Get the sender ID for outgoing messages.
     *
     * @return string
     */
    public function getSenderId(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_SENDER_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get the list of admin phone numbers for alerts.
     *
     * @return array
     */
    public function getAdminPhones(): array
    {
        $value = (string) $this->scopeConfig->getValue(
            self::XML_PATH_ADMIN_PHONES,
            ScopeInterface::SCOPE_STORE
        );

        if ($value === '') {
            return [];
        }

        return array_filter(array_map('trim', explode(',', $value)));
    }

    /**
     * Check if a specific integration event type is enabled.
     *
     * @param string $eventType
     * @return bool
     */
    public function isIntegrationEnabled(string $eventType): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_INTEGRATIONS . $eventType,
            ScopeInterface::SCOPE_STORE
        );
    }
}
