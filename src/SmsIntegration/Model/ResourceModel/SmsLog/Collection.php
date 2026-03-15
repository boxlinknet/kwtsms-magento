<?php
/**
 * SMS Log Collection.
 *
 * Provides iterable access to SMS log records from the kwtsms_sms_log table.
 *
 * @see \KwtSms\SmsIntegration\Model\SmsLog
 * @see \KwtSms\SmsIntegration\Model\ResourceModel\SmsLog
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model\ResourceModel\SmsLog;

use KwtSms\SmsIntegration\Model\SmsLog as SmsLogModel;
use KwtSms\SmsIntegration\Model\ResourceModel\SmsLog as SmsLogResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(SmsLogModel::class, SmsLogResource::class);
    }
}
