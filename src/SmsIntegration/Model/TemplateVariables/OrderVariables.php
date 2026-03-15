<?php
/**
 * Order Template Variables.
 *
 * Resolves placeholder variables from a Magento sales order entity.
 * Provides common order data such as increment ID, status, totals,
 * customer details, and store information for SMS template rendering.
 *
 * @see \KwtSms\SmsIntegration\Model\TemplateProcessor
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model\TemplateVariables;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;

class OrderVariables
{
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var PriceCurrencyInterface
     */
    private PriceCurrencyInterface $priceCurrency;

    /**
     * @param StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Resolve template variables from a sales order.
     *
     * @param Order $order
     * @return array
     */
    public function resolve(Order $order): array
    {
        $currencyCode = $order->getOrderCurrencyCode();
        $decimals = (strtoupper((string) $currencyCode) === 'KWD') ? 3 : 2;
        $formattedTotal = number_format((float) $order->getGrandTotal(), $decimals);

        return [
            'order_id'       => $order->getIncrementId(),
            'order_status'   => $order->getStatusLabel(),
            'total'          => $formattedTotal,
            'currency'       => $currencyCode,
            'customer_name'  => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
            'customer_email' => $order->getCustomerEmail(),
            'customer_phone' => $order->getBillingAddress()?->getTelephone() ?? '',
            'store_name'     => $order->getStore()->getName(),
            'order_date'     => date('Y-m-d H:i', strtotime((string) $order->getCreatedAt())),
            'item_count'     => $order->getTotalItemCount(),
        ];
    }
}
