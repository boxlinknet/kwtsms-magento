<?php
/**
 * Order Place After Observer.
 *
 * Sends SMS notifications when a new order is placed.
 * Dispatches a customer notification for the new order and, if configured,
 * sends an admin alert to all registered admin phone numbers.
 *
 * @see \KwtSms\SmsIntegration\Model\SmsSender
 * @see \KwtSms\SmsIntegration\Model\TemplateProcessor
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Observer;

use KwtSms\SmsIntegration\Model\Config;
use KwtSms\SmsIntegration\Model\SmsSender;
use KwtSms\SmsIntegration\Model\TemplateProcessor;
use KwtSms\SmsIntegration\Model\TemplateVariables\OrderVariables;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class OrderPlaceAfter implements ObserverInterface
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
     * @var OrderVariables
     */
    private OrderVariables $orderVariables;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Config $config
     * @param SmsSender $smsSender
     * @param TemplateProcessor $templateProcessor
     * @param OrderVariables $orderVariables
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        SmsSender $smsSender,
        TemplateProcessor $templateProcessor,
        OrderVariables $orderVariables,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->smsSender = $smsSender;
        $this->templateProcessor = $templateProcessor;
        $this->orderVariables = $orderVariables;
        $this->logger = $logger;
    }

    /**
     * Execute observer for new order placement.
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

            $order = $observer->getEvent()->getOrder();
            if ($order === null) {
                return;
            }

            $variables = $this->orderVariables->resolve($order);
            $incrementId = $order->getIncrementId();
            $storeLocale = $this->getStoreLocale($order);

            // Customer SMS for new order
            if ($this->config->isIntegrationEnabled('order_new')) {
                $phone = $order->getBillingAddress()?->getTelephone();
                if ($phone) {
                    $message = $this->templateProcessor->render('order_new', $variables, $storeLocale);
                    if ($message !== null) {
                        $this->smsSender->send($phone, $message, 'order_new', $incrementId, 'order');
                    }
                }
            }

            // Admin SMS for new order
            if ($this->config->isIntegrationEnabled('admin_new_order')) {
                $adminPhones = $this->config->getAdminPhones();
                foreach ($adminPhones as $adminPhone) {
                    $message = $this->templateProcessor->render('admin_new_order', $variables);
                    if ($message !== null) {
                        $this->smsSender->send($adminPhone, $message, 'admin_new_order', $incrementId, 'order');
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->logger->error('kwtSMS: OrderPlaceAfter observer failed', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the store locale from the order's store.
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string|null
     */
    private function getStoreLocale(\Magento\Sales\Model\Order $order): ?string
    {
        try {
            return $order->getStore()->getConfig('general/locale/code');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
