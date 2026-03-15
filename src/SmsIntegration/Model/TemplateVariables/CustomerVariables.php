<?php
/**
 * Customer Template Variables.
 *
 * Resolves placeholder variables from a Magento customer entity.
 * Provides customer name, email, and store name for SMS template rendering.
 *
 * @see \KwtSms\SmsIntegration\Model\TemplateProcessor
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model\TemplateVariables;

use Magento\Customer\Api\Data\CustomerInterface;

class CustomerVariables
{
    /**
     * Resolve template variables from a customer entity.
     *
     * @param CustomerInterface $customer
     * @param string|null $storeName
     * @return array
     */
    public function resolve(CustomerInterface $customer, ?string $storeName = null): array
    {
        return [
            'customer_name'  => $customer->getFirstname() . ' ' . $customer->getLastname(),
            'customer_email' => $customer->getEmail(),
            'store_name'     => $storeName ?? '',
        ];
    }
}
