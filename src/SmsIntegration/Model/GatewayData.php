<?php
/**
 * Gateway Data Model.
 *
 * Concrete implementation of GatewayDataInterface backed by the kwtsms_gateway_data table.
 * Stores cached gateway information such as account balance, sender IDs, and coverage data.
 *
 * @see \KwtSms\SmsIntegration\Api\Data\GatewayDataInterface
 * @see \KwtSms\SmsIntegration\Model\ResourceModel\GatewayData
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model;

use KwtSms\SmsIntegration\Api\Data\GatewayDataInterface;
use KwtSms\SmsIntegration\Model\ResourceModel\GatewayData as GatewayDataResource;
use Magento\Framework\Model\AbstractModel;

class GatewayData extends AbstractModel implements GatewayDataInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(GatewayDataResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId(): ?int
    {
        $value = $this->getData(self::ENTITY_ID);
        return $value !== null ? (int) $value : null;
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId): GatewayDataInterface
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getDataType(): ?string
    {
        return $this->getData(self::DATA_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setDataType(string $dataType): GatewayDataInterface
    {
        return $this->setData(self::DATA_TYPE, $dataType);
    }

    /**
     * @inheritdoc
     */
    public function getDataValue(): ?string
    {
        return $this->getData(self::DATA_VALUE);
    }

    /**
     * @inheritdoc
     */
    public function setDataValue(?string $dataValue): GatewayDataInterface
    {
        return $this->setData(self::DATA_VALUE, $dataValue);
    }

    /**
     * @inheritdoc
     */
    public function getSyncedAt(): ?string
    {
        return $this->getData(self::SYNCED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setSyncedAt(?string $syncedAt): GatewayDataInterface
    {
        return $this->setData(self::SYNCED_AT, $syncedAt);
    }
}
