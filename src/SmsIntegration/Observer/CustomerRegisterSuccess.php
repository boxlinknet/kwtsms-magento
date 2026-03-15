<?php
/**
 * Customer Register Success Observer.
 *
 * Sends SMS notifications when a new customer registers.
 * Dispatches a welcome message to the customer (if a phone number is available
 * from the default billing address) and an admin alert to all configured
 * admin phone numbers.
 *
 * @see \KwtSms\SmsIntegration\Model\SmsSender
 * @see \KwtSms\SmsIntegration\Model\TemplateProcessor
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Observer;

use KwtSms\SmsIntegration\Model\Config;
use KwtSms\SmsIntegration\Model\SmsSender;
use KwtSms\SmsIntegration\Model\TemplateProcessor;
use KwtSms\SmsIntegration\Model\TemplateVariables\CustomerVariables;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class CustomerRegisterSuccess implements ObserverInterface
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var SmsSender
     */
    private SmsSender $smsSender;

    /**
     * @var TemplateProcessor
     */
    private TemplateProcessor $templateProcessor;

    /**
     * @var CustomerVariables
     */
    private CustomerVariables $customerVariables;

    /**
     * @var AddressRepositoryInterface
     */
    private AddressRepositoryInterface $addressRepository;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Config $config
     * @param SmsSender $smsSender
     * @param TemplateProcessor $templateProcessor
     * @param CustomerVariables $customerVariables
     * @param AddressRepositoryInterface $addressRepository
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        SmsSender $smsSender,
        TemplateProcessor $templateProcessor,
        CustomerVariables $customerVariables,
        AddressRepositoryInterface $addressRepository,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->smsSender = $smsSender;
        $this->templateProcessor = $templateProcessor;
        $this->customerVariables = $customerVariables;
        $this->addressRepository = $addressRepository;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * Execute observer for customer registration.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        try {
            if (!$this->config->isEnabled()) {
                return;
            }

            $customer = $observer->getEvent()->getCustomer();
            if ($customer === null) {
                return;
            }

            $storeName = $this->getStoreName();
            $variables = $this->customerVariables->resolve($customer, $storeName);
            $customerId = $customer->getId();

            // Customer welcome SMS
            if ($this->config->isIntegrationEnabled('customer_welcome')) {
                $phone = $this->getCustomerPhone($customer);
                if ($phone) {
                    $storeLocale = $this->getStoreLocale();
                    $message = $this->templateProcessor->render('customer_welcome', $variables, $storeLocale);
                    if ($message !== null) {
                        $this->smsSender->send(
                            $phone,
                            $message,
                            'customer_welcome',
                            $customerId ? (string) $customerId : null,
                            'customer'
                        );
                    }
                }
            }

            // Admin alert for new customer
            if ($this->config->isIntegrationEnabled('admin_new_customer')) {
                $adminPhones = $this->config->getAdminPhones();
                foreach ($adminPhones as $adminPhone) {
                    $message = $this->templateProcessor->render('admin_new_customer', $variables);
                    if ($message !== null) {
                        $this->smsSender->send(
                            $adminPhone,
                            $message,
                            'admin_new_customer',
                            $customerId ? (string) $customerId : null,
                            'customer'
                        );
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->logger->error('kwtSMS: CustomerRegisterSuccess observer failed', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Attempt to retrieve the customer phone number from the default billing address.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return string|null
     */
    private function getCustomerPhone(\Magento\Customer\Api\Data\CustomerInterface $customer): ?string
    {
        try {
            $billingAddressId = $customer->getDefaultBilling();
            if ($billingAddressId) {
                $address = $this->addressRepository->getById((int) $billingAddressId);
                $phone = $address->getTelephone();
                if ($phone) {
                    return $phone;
                }
            }
        } catch (\Throwable $e) {
            $this->logger->debug('kwtSMS: Could not retrieve customer phone at registration', [
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Get the current store name.
     *
     * @return string
     */
    private function getStoreName(): string
    {
        try {
            return (string) $this->storeManager->getStore()->getName();
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Get the current store locale.
     *
     * @return string|null
     */
    private function getStoreLocale(): ?string
    {
        try {
            return $this->storeManager->getStore()->getConfig('general/locale/code');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
