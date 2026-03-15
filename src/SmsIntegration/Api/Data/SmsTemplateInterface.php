<?php
/**
 * SMS Template Data Interface.
 *
 * Defines the data contract for SMS templates stored in the kwtsms_sms_template table.
 * Templates hold translatable message bodies tied to specific store events.
 *
 * @see \KwtSms\SmsIntegration\Model\SmsTemplate
 * @see \KwtSms\SmsIntegration\Api\SmsTemplateRepositoryInterface
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Api\Data;

interface SmsTemplateInterface
{
    /**
     * Table name constant.
     */
    const TABLE_NAME = 'kwtsms_sms_template';

    /**
     * Column name constants.
     */
    const ENTITY_ID = 'entity_id';
    const EVENT_TYPE = 'event_type';
    const NAME = 'name';
    const MESSAGE_EN = 'message_en';
    const MESSAGE_AR = 'message_ar';
    const IS_ACTIVE = 'is_active';
    const RECIPIENT_TYPE = 'recipient_type';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Recipient type constants.
     */
    const RECIPIENT_CUSTOMER = 'customer';
    const RECIPIENT_ADMIN = 'admin';
    const RECIPIENT_BOTH = 'both';

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
     * Get event type.
     *
     * @return string|null
     */
    public function getEventType(): ?string;

    /**
     * Set event type.
     *
     * @param string $eventType
     * @return $this
     */
    public function setEventType(string $eventType): self;

    /**
     * Get template name.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set template name.
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self;

    /**
     * Get English message body.
     *
     * @return string|null
     */
    public function getMessageEn(): ?string;

    /**
     * Set English message body.
     *
     * @param string $messageEn
     * @return $this
     */
    public function setMessageEn(string $messageEn): self;

    /**
     * Get Arabic message body.
     *
     * @return string|null
     */
    public function getMessageAr(): ?string;

    /**
     * Set Arabic message body.
     *
     * @param string|null $messageAr
     * @return $this
     */
    public function setMessageAr(?string $messageAr): self;

    /**
     * Get active status flag.
     *
     * @return int
     */
    public function getIsActive(): int;

    /**
     * Set active status flag.
     *
     * @param int $isActive
     * @return $this
     */
    public function setIsActive(int $isActive): self;

    /**
     * Get recipient type.
     *
     * @return string|null
     */
    public function getRecipientType(): ?string;

    /**
     * Set recipient type.
     *
     * @param string $recipientType
     * @return $this
     */
    public function setRecipientType(string $recipientType): self;

    /**
     * Get creation timestamp.
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * Set creation timestamp.
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self;

    /**
     * Get last update timestamp.
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * Set last update timestamp.
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): self;
}
