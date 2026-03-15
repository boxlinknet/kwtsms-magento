<?php
/**
 * SMS Log Resource Model.
 *
 * Manages database persistence for the kwtsms_sms_log table.
 *
 * @see \KwtSms\SmsIntegration\Model\SmsLog
 * @see \KwtSms\SmsIntegration\Api\Data\SmsLogInterface
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model\ResourceModel;

use KwtSms\SmsIntegration\Api\Data\SmsLogInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SmsLog extends AbstractDb
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(SmsLogInterface::TABLE_NAME, SmsLogInterface::ENTITY_ID);
    }
}
