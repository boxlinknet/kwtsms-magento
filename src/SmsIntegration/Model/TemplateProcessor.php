<?php
/**
 * Template Processor.
 *
 * Loads SMS templates by event type and renders them by replacing
 * placeholder tokens with the supplied variable values.
 * Supports locale-aware message selection (English / Arabic).
 *
 * @see \KwtSms\SmsIntegration\Api\SmsTemplateRepositoryInterface
 * @see \KwtSms\SmsIntegration\Model\MessageCleaner
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model;

use KwtSms\SmsIntegration\Api\SmsTemplateRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class TemplateProcessor
{
    /**
     * @var SmsTemplateRepositoryInterface
     */
    private SmsTemplateRepositoryInterface $templateRepository;

    /**
     * @var MessageCleaner
     */
    private MessageCleaner $messageCleaner;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param SmsTemplateRepositoryInterface $templateRepository
     * @param MessageCleaner $messageCleaner
     * @param LoggerInterface $logger
     */
    public function __construct(
        SmsTemplateRepositoryInterface $templateRepository,
        MessageCleaner $messageCleaner,
        LoggerInterface $logger
    ) {
        $this->templateRepository = $templateRepository;
        $this->messageCleaner = $messageCleaner;
        $this->logger = $logger;
    }

    /**
     * Render a template for the given event type with variable substitution.
     *
     * Loads the template by event type, selects the appropriate language variant
     * based on locale, and replaces {{placeholder}} tokens with values from the
     * supplied variables array. Placeholders without a matching variable are left as-is.
     *
     * @param string $eventType
     * @param array $variables
     * @param string|null $locale
     * @return string|null
     */
    public function render(string $eventType, array $variables, ?string $locale = null): ?string
    {
        try {
            $template = $this->templateRepository->getByEventType($eventType);
        } catch (NoSuchEntityException $e) {
            $this->logger->debug('kwtSMS: No template found for event type: ' . $eventType);
            return null;
        }

        if (!$template->getIsActive()) {
            $this->logger->debug('kwtSMS: Template for event type is inactive: ' . $eventType);
            return null;
        }

        // Select message based on locale
        $message = null;
        if ($locale !== null && str_starts_with($locale, 'ar')) {
            $message = $template->getMessageAr();
        }

        // Fallback to English if Arabic message is not available or locale is not Arabic
        if ($message === null || $message === '') {
            $message = $template->getMessageEn();
        }

        if ($message === null || $message === '') {
            $this->logger->debug('kwtSMS: Template message is empty for event type: ' . $eventType);
            return null;
        }

        // Replace placeholders
        $rendered = preg_replace_callback(
            '/\{\{(\w+)\}\}/',
            function (array $matches) use ($variables): string {
                $key = $matches[1];
                if (array_key_exists($key, $variables)) {
                    return (string) $variables[$key];
                }
                return $matches[0];
            },
            $message
        );

        return $rendered;
    }
}
