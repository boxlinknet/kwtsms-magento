<?php
/**
 * Gateway Data Interface.
 *
 * Defines the data contract for cached gateway information stored in the kwtsms_gateway_data table.
 * Holds synced snapshots of account balance, sender IDs, and coverage data from the kwtSMS API.
 *
 * @see \KwtSms\SmsIntegration\Model\GatewayData
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Api\Data;

interface GatewayDataInterface
{
    /**
     * Table name constant.
     */
    const TABLE_NAME = 'kwtsms_gateway_data';

    /**
     * Column name constants.
     */
    const ENTITY_ID = 'entity_id';
    const DATA_TYPE = 'data_type';
    const DATA_VALUE = 'data_value';
    const SYNCED_AT = 'synced_at';

    /**
     * Data type constants.
     */
    const TYPE_BALANCE = 'balance';
    const TYPE_SENDERID = 'senderid';
    const TYPE_COVERAGE = 'coverage';

    /**
     * Get entity ID.
     *
     * @return int|null
     */
    public function getEntityId(): ?int;

    /**
     * Set entity ID.
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId): self;

    /**
     * Get data type.
     *
     * @return string|null
     */
    public function getDataType(): ?string;

    /**
     * Set data type.
     *
     * @param string $dataType
     * @return $this
     */
    public function setDataType(string $dataType): self;

    /**
     * Get data value.
     *
     * @return string|null
     */
    public function getDataValue(): ?string;

    /**
     * Set data value.
     *
     * @param string|null $dataValue
     * @return $this
     */
    public function setDataValue(?string $dataValue): self;

    /**
     * Get last sync timestamp.
     *
     * @return string|null
     */
    public function getSyncedAt(): ?string;

    /**
     * Set last sync timestamp.
     *
     * @param string|null $syncedAt
     * @return $this
     */
    public function setSyncedAt(?string $syncedAt): self;
}
