<?php
/**
 * SMS Template Collection.
 *
 * Provides iterable access to SMS template records from the kwtsms_sms_template table.
 *
 * @see \KwtSms\SmsIntegration\Model\SmsTemplate
 * @see \KwtSms\SmsIntegration\Model\ResourceModel\SmsTemplate
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model\ResourceModel\SmsTemplate;

use KwtSms\SmsIntegration\Model\SmsTemplate as SmsTemplateModel;
use KwtSms\SmsIntegration\Model\ResourceModel\SmsTemplate as SmsTemplateResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(SmsTemplateModel::class, SmsTemplateResource::class);
    }
}
