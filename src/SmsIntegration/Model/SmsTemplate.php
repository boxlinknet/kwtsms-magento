<?php
/**
 * SMS Template Model.
 *
 * Concrete implementation of SmsTemplateInterface backed by the kwtsms_sms_template table.
 * All getters and setters delegate to the underlying AbstractModel data store.
 *
 * @see \KwtSms\SmsIntegration\Api\Data\SmsTemplateInterface
 * @see \KwtSms\SmsIntegration\Model\ResourceModel\SmsTemplate
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model;

use KwtSms\SmsIntegration\Api\Data\SmsTemplateInterface;
use KwtSms\SmsIntegration\Model\ResourceModel\SmsTemplate as SmsTemplateResource;
use Magento\Framework\Model\AbstractModel;

class SmsTemplate extends AbstractModel implements SmsTemplateInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(SmsTemplateResource::class);
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
    public function setEntityId($entityId): SmsTemplateInterface
    {
        return $this->setData(self::ENTITY_ID, $entityId);
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
    public function setEventType(string $eventType): SmsTemplateInterface
    {
        return $this->setData(self::EVENT_TYPE, $eventType);
    }

    /**
     * @inheritdoc
     */
    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name): SmsTemplateInterface
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getMessageEn(): ?string
    {
        return $this->getData(self::MESSAGE_EN);
    }

    /**
     * @inheritdoc
     */
    public function setMessageEn(string $messageEn): SmsTemplateInterface
    {
        return $this->setData(self::MESSAGE_EN, $messageEn);
    }

    /**
     * @inheritdoc
     */
    public function getMessageAr(): ?string
    {
        return $this->getData(self::MESSAGE_AR);
    }

    /**
     * @inheritdoc
     */
    public function setMessageAr(?string $messageAr): SmsTemplateInterface
    {
        return $this->setData(self::MESSAGE_AR, $messageAr);
    }

    /**
     * @inheritdoc
     */
    public function getIsActive(): int
    {
        return (int) $this->getData(self::IS_ACTIVE);
    }

    /**
     * @inheritdoc
     */
    public function setIsActive(int $isActive): SmsTemplateInterface
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @inheritdoc
     */
    public function getRecipientType(): ?string
    {
        return $this->getData(self::RECIPIENT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setRecipientType(string $recipientType): SmsTemplateInterface
    {
        return $this->setData(self::RECIPIENT_TYPE, $recipientType);
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
    public function setCreatedAt(string $createdAt): SmsTemplateInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt(string $updatedAt): SmsTemplateInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
