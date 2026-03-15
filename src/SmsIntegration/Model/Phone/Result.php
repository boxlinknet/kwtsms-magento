<?php
/**
 * Phone Normalization Result.
 *
 * Immutable value object that carries the outcome of a phone number normalization attempt.
 * Contains the validity flag, the normalized number, and an optional error message.
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model\Phone;

class Result
{
    /**
     * @var bool
     */
    private bool $valid;

    /**
     * @var string
     */
    private string $normalized;

    /**
     * @var string|null
     */
    private ?string $error;

    /**
     * @param bool $valid
     * @param string $normalized
     * @param string|null $error
     */
    public function __construct(bool $valid, string $normalized, ?string $error = null)
    {
        $this->valid = $valid;
        $this->normalized = $normalized;
        $this->error = $error;
    }

    /**
     * Whether the phone number is valid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * Get the normalized phone number.
     *
     * @return string
     */
    public function getNormalized(): string
    {
        return $this->normalized;
    }

    /**
     * Get the error message, if any.
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }
}
