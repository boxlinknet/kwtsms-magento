<?php
/**
 * Shipment Save After Observer.
 *
 * Sends an SMS notification when a shipment is created or updated.
 * Includes tracking number and carrier information in the message
 * variables alongside standard order data.
 *
 * @see \KwtSms\SmsIntegration\Model\SmsSender
 * @see \KwtSms\SmsIntegration\Model\TemplateVariables\ShipmentVariables
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Observer;

use KwtSms\SmsIntegration\Model\Config;
use KwtSms\SmsIntegration\Model\SmsSender;
use KwtSms\SmsIntegration\Model\TemplateProcessor;
use KwtSms\SmsIntegration\Model\TemplateVariables\ShipmentVariables;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class ShipmentSaveAfter implements ObserverInterface
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
     * @var ShipmentVariables
     */
    private ShipmentVariables $shipmentVariables;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Config $config
     * @param SmsSender $smsSender
     * @param TemplateProcessor $templateProcessor
     * @param ShipmentVariables $shipmentVariables
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        SmsSender $smsSender,
        TemplateProcessor $templateProcessor,
        ShipmentVariables $shipmentVariables,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->smsSender = $smsSender;
        $this->templateProcessor = $templateProcessor;
        $this->shipmentVariables = $shipmentVariables;
        $this->logger = $logger;
    }

    /**
     * Execute observer for shipment save.
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

            if (!$this->config->isIntegrationEnabled('order_shipped')) {
                return;
            }

            $shipment = $observer->getEvent()->getShipment();
            if ($shipment === null) {
                return;
            }

            $order = $shipment->getOrder();
            $phone = $order->getBillingAddress()?->getTelephone();
            if (!$phone) {
                return;
            }

            $variables = $this->shipmentVariables->resolve($shipment);
            $storeLocale = $this->getStoreLocale($order);
            $message = $this->templateProcessor->render('order_shipped', $variables, $storeLocale);

            if ($message !== null) {
                $this->smsSender->send(
                    $phone,
                    $message,
                    'order_shipped',
                    $order->getIncrementId(),
                    'order'
                );
            }
        } catch (\Throwable $e) {
            $this->logger->error('kwtSMS: ShipmentSaveAfter observer failed', [
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
