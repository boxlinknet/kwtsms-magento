<?php
/**
 * SMS Log Model.
 *
 * Concrete implementation of SmsLogInterface backed by the kwtsms_sms_log table.
 * All getters and setters delegate to the underlying AbstractModel data store.
 *
 * @see \KwtSms\SmsIntegration\Api\Data\SmsLogInterface
 * @see \KwtSms\SmsIntegration\Model\ResourceModel\SmsLog
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model;

use KwtSms\SmsIntegration\Api\Data\SmsLogInterface;
use KwtSms\SmsIntegration\Model\ResourceModel\SmsLog as SmsLogResource;
use Magento\Framework\Model\AbstractModel;

class SmsLog extends AbstractModel implements SmsLogInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(SmsLogResource::class);
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
    public function setEntityId($entityId): SmsLogInterface
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getRecipient(): ?string
    {
        return $this->getData(self::RECIPIENT);
    }

    /**
     * @inheritdoc
     */
    public function setRecipient(string $recipient): SmsLogInterface
    {
        return $this->setData(self::RECIPIENT, $recipient);
    }

    /**
     * @inheritdoc
     */
    public function getMessage(): ?string
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @inheritdoc
     */
    public function setMessage(string $message): SmsLogInterface
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritdoc
     */
    public function getEventType(): ?string
    {
        return $this->getData(self::EVENT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setEventType(string $eventType): SmsLogInterface
    {
        return $this->setData(self::EVENT_TYPE, $eventType);
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus(string $status): SmsLogInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getApiResponse(): ?string
    {
        return $this->getData(self::API_RESPONSE);
    }

    /**
     * @inheritdoc
     */
    public function setApiResponse(?string $apiResponse): SmsLogInterface
    {
        return $this->setData(self::API_RESPONSE, $apiResponse);
    }

    /**
     * @inheritdoc
     */
    public function getMsgId(): ?string
    {
        return $this->getData(self::MSG_ID);
    }

    /**
     * @inheritdoc
     */
    public function setMsgId(?string $msgId): SmsLogInterface
    {
        return $this->setData(self::MSG_ID, $msgId);
    }

    /**
     * @inheritdoc
     */
    public function getPointsCharged(): int
    {
        return (int) $this->getData(self::POINTS_CHARGED);
    }

    /**
     * @inheritdoc
     */
    public function setPointsCharged(int $pointsCharged): SmsLogInterface
    {
        return $this->setData(self::POINTS_CHARGED, $pointsCharged);
    }

    /**
     * @inheritdoc
     */
    public function getErrorCode(): ?string
    {
        return $this->getData(self::ERROR_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setErrorCode(?string $errorCode): SmsLogInterface
    {
        return $this->setData(self::ERROR_CODE, $errorCode);
    }

    /**
     * @inheritdoc
     */
    public function getRelatedEntityId(): ?string
    {
        return $this->getData(self::RELATED_ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setRelatedEntityId(?string $relatedEntityId): SmsLogInterface
    {
        return $this->setData(self::RELATED_ENTITY_ID, $relatedEntityId);
    }

    /**
     * @inheritdoc
     */
    public function getRelatedEntityType(): ?string
    {
        return $this->getData(self::RELATED_ENTITY_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setRelatedEntityType(?string $relatedEntityType): SmsLogInterface
    {
        return $this->setData(self::RELATED_ENTITY_TYPE, $relatedEntityType);
    }

    /**
     * @inheritdoc
     */
    public function getTestMode(): int
    {
        return (int) $this->getData(self::TEST_MODE);
    }

    /**
     * @inheritdoc
     */
    public function setTestMode(int $testMode): SmsLogInterface
    {
        return $this->setData(self::TEST_MODE, $testMode);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt(string $createdAt): SmsLogInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
