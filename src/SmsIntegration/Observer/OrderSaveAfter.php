<?php
/**
 * Order Save After Observer.
 *
 * Sends an SMS notification when an order status changes.
 * Only triggers when the status value has actually changed compared to
 * the original data, preventing duplicate notifications on unrelated saves.
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

class OrderSaveAfter implements ObserverInterface
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
     * Execute observer for order status change.
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

            $origStatus = $order->getOrigData('status');
            $currentStatus = $order->getStatus();

            // No status change, nothing to do
            if ($origStatus === $currentStatus) {
                return;
            }

            $phone = $order->getBillingAddress()?->getTelephone();
            $incrementId = $order->getIncrementId();
            $variables = $this->orderVariables->resolve($order);
            $storeLocale = $this->getStoreLocale($order);

            // Detect brand-new order: origStatus is null (first save)
            $isNewOrder = ($origStatus === null || $origStatus === '');

            if ($isNewOrder) {
                // Send "new order" customer SMS
                if ($this->config->isIntegrationEnabled('order_new') && $phone) {
                    $message = $this->templateProcessor->render('order_new', $variables, $storeLocale);
                    if ($message !== null) {
                        $this->smsSender->send($phone, $message, 'order_new', $incrementId, 'order');
                    }
                }

                // Send "new order" admin alert
                if ($this->config->isIntegrationEnabled('admin_new_order')) {
                    $adminPhones = $this->config->getAdminPhones();
                    foreach ($adminPhones as $adminPhone) {
                        $message = $this->templateProcessor->render('admin_new_order', $variables);
                        if ($message !== null) {
                            $this->smsSender->send($adminPhone, $message, 'admin_new_order', $incrementId, 'order');
                        }
                    }
                }
            } else {
                // Existing order, status changed: send status update SMS
                if ($this->config->isIntegrationEnabled('order_status_change') && $phone) {
                    $message = $this->templateProcessor->render('order_status_change', $variables, $storeLocale);
                    if ($message !== null) {
                        $this->smsSender->send($phone, $message, 'order_status_change', $incrementId, 'order');
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->logger->error('kwtSMS: OrderSaveAfter observer failed', [
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
