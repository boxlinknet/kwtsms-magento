<?php
/**
 * SMS Template Resource Model.
 *
 * Manages database persistence for the kwtsms_sms_template table.
 *
 * @see \KwtSms\SmsIntegration\Model\SmsTemplate
 * @see \KwtSms\SmsIntegration\Api\Data\SmsTemplateInterface
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model\ResourceModel;

use KwtSms\SmsIntegration\Api\Data\SmsTemplateInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SmsTemplate extends AbstractDb
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(SmsTemplateInterface::TABLE_NAME, SmsTemplateInterface::ENTITY_ID);
    }
}
