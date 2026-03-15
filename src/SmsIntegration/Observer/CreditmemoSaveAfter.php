<?php
/**
 * Credit Memo Save After Observer.
 *
 * Sends an SMS notification when a credit memo (refund) is created.
 * Overrides the {{total}} variable with the credit memo grand total
 * instead of the original order total.
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

class CreditmemoSaveAfter implements ObserverInterface
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
     * Execute observer for credit memo save.
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

            if (!$this->config->isIntegrationEnabled('order_refunded')) {
                return;
            }

            $creditmemo = $observer->getEvent()->getCreditmemo();
            if ($creditmemo === null) {
                return;
            }

            $order = $creditmemo->getOrder();
            $phone = $order->getBillingAddress()?->getTelephone();
            if (!$phone) {
                return;
            }

            $variables = $this->orderVariables->resolve($order);

            // Override total with credit memo amount
            $currencyCode = $order->getOrderCurrencyCode();
            $decimals = (strtoupper((string) $currencyCode) === 'KWD') ? 3 : 2;
            $variables['total'] = number_format((float) $creditmemo->getGrandTotal(), $decimals);

            $storeLocale = $this->getStoreLocale($order);
            $message = $this->templateProcessor->render('order_refunded', $variables, $storeLocale);

            if ($message !== null) {
                $this->smsSender->send(
                    $phone,
                    $message,
                    'order_refunded',
                    $order->getIncrementId(),
                    'order'
                );
            }
        } catch (\Throwable $e) {
            $this->logger->error('kwtSMS: CreditmemoSaveAfter observer failed', [
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
