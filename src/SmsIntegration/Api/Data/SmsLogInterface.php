<?php
/**
 * SMS Log Data Interface.
 *
 * Defines the data contract for SMS log entries stored in the kwtsms_sms_log table.
 * Each record represents a single SMS dispatch attempt with its status and API response details.
 *
 * @see \KwtSms\SmsIntegration\Model\SmsLog
 * @see \KwtSms\SmsIntegration\Api\SmsLogRepositoryInterface
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Api\Data;

interface SmsLogInterface
{
    /**
     * Table name constant.
     */
    const TABLE_NAME = 'kwtsms_sms_log';

    /**
     * Column name constants.
     */
    const ENTITY_ID = 'entity_id';
    const RECIPIENT = 'recipient';
    const MESSAGE = 'message';
    const EVENT_TYPE = 'event_type';
    const STATUS = 'status';
    const API_RESPONSE = 'api_response';
    const MSG_ID = 'msg_id';
    const POINTS_CHARGED = 'points_charged';
    const ERROR_CODE = 'error_code';
    const RELATED_ENTITY_ID = 'related_entity_id';
    const RELATED_ENTITY_TYPE = 'related_entity_type';
    const TEST_MODE = 'test_mode';
    const CREATED_AT = 'created_at';

    /**
     * Status value constants.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';
    const STATUS_QUEUED = 'queued';
    const STATUS_TEST = 'test';
    const STATUS_SKIPPED = 'skipped';

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
     * Get recipient phone number.
     *
     * @return string|null
     */
    public function getRecipient(): ?string;

    /**
     * Set recipient phone number.
     *
     * @param string $recipient
     * @return $this
     */
    public function setRecipient(string $recipient): self;

    /**
     * Get SMS message body.
     *
     * @return string|null
     */
    public function getMessage(): ?string;

    /**
     * Set SMS message body.
     *
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): self;

    /**
     * Get event type that triggered this SMS.
     *
     * @return string|null
     */
    public function getEventType(): ?string;

    /**
     * Set event type that triggered this SMS.
     *
     * @param string $eventType
     * @return $this
     */
    public function setEventType(string $eventType): self;

    /**
     * Get delivery status.
     *
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * Set delivery status.
     *
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self;

    /**
     * Get raw API response.
     *
     * @return string|null
     */
    public function getApiResponse(): ?string;

    /**
     * Set raw API response.
     *
     * @param string|null $apiResponse
     * @return $this
     */
    public function setApiResponse(?string $apiResponse): self;

    /**
     * Get message ID returned by the gateway.
     *
     * @return string|null
     */
    public function getMsgId(): ?string;

    /**
     * Set message ID returned by the gateway.
     *
     * @param string|null $msgId
     * @return $this
     */
    public function setMsgId(?string $msgId): self;

    /**
     * Get number of points charged for this SMS.
     *
     * @return int
     */
    public function getPointsCharged(): int;

    /**
     * Set number of points charged for this SMS.
     *
     * @param int $pointsCharged
     * @return $this
     */
    public function setPointsCharged(int $pointsCharged): self;

    /**
     * Get error code from the gateway, if any.
     *
     * @return string|null
     */
    public function getErrorCode(): ?string;

    /**
     * Set error code from the gateway.
     *
     * @param string|null $errorCode
     * @return $this
     */
    public function setErrorCode(?string $errorCode): self;

    /**
     * Get the ID of the related entity (order, shipment, etc.).
     *
     * @return string|null
     */
    public function getRelatedEntityId(): ?string;

    /**
     * Set the ID of the related entity.
     *
     * @param string|null $relatedEntityId
     * @return $this
     */
    public function setRelatedEntityId(?string $relatedEntityId): self;

    /**
     * Get the type of the related entity (order, shipment, etc.).
     *
     * @return string|null
     */
    public function getRelatedEntityType(): ?string;

    /**
     * Set the type of the related entity.
     *
     * @param string|null $relatedEntityType
     * @return $this
     */
    public function setRelatedEntityType(?string $relatedEntityType): self;

    /**
     * Get test mode flag.
     *
     * @return int
     */
    public function getTestMode(): int;

    /**
     * Set test mode flag.
     *
     * @param int $testMode
     * @return $this
     */
    public function setTestMode(int $testMode): self;

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
}
