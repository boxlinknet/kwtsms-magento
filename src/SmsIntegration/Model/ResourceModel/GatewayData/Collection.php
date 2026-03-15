<?php
/**
 * Gateway Data Collection.
 *
 * Provides iterable access to gateway data records from the kwtsms_gateway_data table.
 *
 * @see \KwtSms\SmsIntegration\Model\GatewayData
 * @see \KwtSms\SmsIntegration\Model\ResourceModel\GatewayData
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model\ResourceModel\GatewayData;

use KwtSms\SmsIntegration\Model\GatewayData as GatewayDataModel;
use KwtSms\SmsIntegration\Model\ResourceModel\GatewayData as GatewayDataResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(GatewayDataModel::class, GatewayDataResource::class);
    }
}
