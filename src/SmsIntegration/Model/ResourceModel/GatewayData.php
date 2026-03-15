<?php
/**
 * Gateway Data Resource Model.
 *
 * Manages database persistence for the kwtsms_gateway_data table.
 *
 * @see \KwtSms\SmsIntegration\Model\GatewayData
 * @see \KwtSms\SmsIntegration\Api\Data\GatewayDataInterface
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model\ResourceModel;

use KwtSms\SmsIntegration\Api\Data\GatewayDataInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class GatewayData extends AbstractDb
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(GatewayDataInterface::TABLE_NAME, GatewayDataInterface::ENTITY_ID);
    }
}
