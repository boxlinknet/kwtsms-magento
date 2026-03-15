<?php

/**
 * kwtSMS SMS Integration - Default Templates Data Patch
 *
 * Inserts default SMS templates for all supported event types
 * with English and Arabic message bodies.
 *
 * Related: etc/db_schema.xml (kwtsms_sms_template table)
 */

declare(strict_types=1);

namespace KwtSms\SmsIntegration\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InsertDefaultTemplates implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply(): self
    {
        $this->moduleDataSetup->startSetup();

        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('kwtsms_sms_template');

        $templates = [
            [
                'event_type' => 'order_new',
                'name' => 'New Order',
                'message_en' => 'Your order #{{order_id}} has been placed successfully. Thank you for shopping with {{store_name}}!',
                'message_ar' => 'تم استلام طلبك #{{order_id}} بنجاح. شكرا لتسوقك مع {{store_name}}!',
                'is_active' => 1,
                'recipient_type' => 'customer',
            ],
            [
                'event_type' => 'order_status_change',
                'name' => 'Order Status Update',
                'message_en' => 'Your order #{{order_id}} status has been updated to: {{order_status}}.',
                'message_ar' => 'تم تحديث حالة طلبك #{{order_id}} الى: {{order_status}}.',
                'is_active' => 1,
                'recipient_type' => 'customer',
            ],
            [
                'event_type' => 'order_shipped',
                'name' => 'Order Shipped',
                'message_en' => 'Your order #{{order_id}} has been shipped. Tracking: {{tracking_number}}.',
                'message_ar' => 'تم شحن طلبك #{{order_id}}. رقم التتبع: {{tracking_number}}.',
                'is_active' => 1,
                'recipient_type' => 'customer',
            ],
            [
                'event_type' => 'order_invoiced',
                'name' => 'Invoice Created',
                'message_en' => 'Payment confirmed for order #{{order_id}}. Amount: {{total}} {{currency}}.',
                'message_ar' => 'تم تاكيد الدفع للطلب #{{order_id}}. المبلغ: {{total}} {{currency}}.',
                'is_active' => 1,
                'recipient_type' => 'customer',
            ],
            [
                'event_type' => 'order_refunded',
                'name' => 'Refund Issued',
                'message_en' => 'A refund of {{total}} {{currency}} has been issued for order #{{order_id}}.',
                'message_ar' => 'تم استرداد مبلغ {{total}} {{currency}} للطلب #{{order_id}}.',
                'is_active' => 1,
                'recipient_type' => 'customer',
            ],
            [
                'event_type' => 'order_cancelled',
                'name' => 'Order Cancelled',
                'message_en' => 'Your order #{{order_id}} has been cancelled.',
                'message_ar' => 'تم الغاء طلبك #{{order_id}}.',
                'is_active' => 1,
                'recipient_type' => 'customer',
            ],
            [
                'event_type' => 'customer_welcome',
                'name' => 'Welcome',
                'message_en' => 'Welcome to {{store_name}}, {{customer_name}}! We are glad to have you.',
                'message_ar' => 'مرحبا بك في {{store_name}}، {{customer_name}}!',
                'is_active' => 1,
                'recipient_type' => 'customer',
            ],
            [
                'event_type' => 'admin_new_order',
                'name' => 'Admin: New Order',
                'message_en' => 'New order #{{order_id}} received. Amount: {{total}} {{currency}}. Customer: {{customer_name}}.',
                'message_ar' => 'طلب جديد #{{order_id}}. المبلغ: {{total}} {{currency}}. العميل: {{customer_name}}.',
                'is_active' => 1,
                'recipient_type' => 'admin',
            ],
            [
                'event_type' => 'admin_new_customer',
                'name' => 'Admin: New Customer',
                'message_en' => 'New customer registered: {{customer_name}} ({{customer_email}}).',
                'message_ar' => 'عميل جديد: {{customer_name}} ({{customer_email}}).',
                'is_active' => 1,
                'recipient_type' => 'admin',
            ],
            [
                'event_type' => 'admin_low_stock',
                'name' => 'Admin: Low Stock',
                'message_en' => 'Low stock alert: {{product_name}} (SKU: {{product_sku}}) has {{stock_qty}} units remaining.',
                'message_ar' => 'تنبيه مخزون منخفض: {{product_name}} (SKU: {{product_sku}}) متبقي {{stock_qty}} وحدة.',
                'is_active' => 1,
                'recipient_type' => 'admin',
            ],
        ];

        foreach ($templates as $template) {
            $connection->insertOnDuplicate($tableName, $template, ['name', 'message_en', 'message_ar']);
        }

        $this->moduleDataSetup->endSetup();

        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
