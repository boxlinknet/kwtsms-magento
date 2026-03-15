<?php
/**
 * Message Cleaner.
 *
 * Sanitizes SMS message content by stripping HTML, emoji, zero-width characters,
 * and normalizing whitespace. Also provides character counting and page calculation.
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model;

class MessageCleaner
{
    /**
     * Clean and sanitize an SMS message body.
     *
     * Strips HTML tags, emoji, zero-width characters, and normalizes whitespace.
     *
     * @param string $message
     * @return string
     */
    public function clean(string $message): string
    {
        // Strip HTML tags
        $message = strip_tags($message);

        // Strip emoji (various Unicode ranges)
        $message = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $message);  // Emoticons
        $message = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $message);  // Misc Symbols and Pictographs
        $message = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $message);  // Transport and Map
        $message = preg_replace('/[\x{1F1E0}-\x{1F1FF}]/u', '', $message);  // Flags
        $message = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $message);    // Misc Symbols
        $message = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $message);    // Dingbats
        $message = preg_replace('/[\x{FE00}-\x{FE0F}]/u', '', $message);    // Variation Selectors
        $message = preg_replace('/[\x{1F900}-\x{1F9FF}]/u', '', $message);  // Supplemental Symbols
        $message = preg_replace('/[\x{1FA00}-\x{1FA6F}]/u', '', $message);  // Chess Symbols
        $message = preg_replace('/[\x{1FA70}-\x{1FAFF}]/u', '', $message);  // Symbols Extended-A
        $message = preg_replace('/[\x{200D}]/u', '', $message);             // Zero-width joiner (emoji sequences)
        $message = preg_replace('/[\x{20E3}]/u', '', $message);             // Combining Enclosing Keycap
        $message = preg_replace('/[\x{E0020}-\x{E007F}]/u', '', $message);  // Tags

        // Strip zero-width characters
        $message = preg_replace('/[\x{200B}\x{200C}\x{200D}\x{FEFF}]/u', '', $message);

        // Normalize whitespace: collapse multiple spaces and newlines
        $message = preg_replace('/[ \t]+/', ' ', $message);
        $message = preg_replace('/\n{3,}/', "\n\n", $message);

        return trim($message);
    }

    /**
     * Get the character count of a message.
     *
     * @param string $message
     * @return int
     */
    public function getCharacterCount(string $message): int
    {
        return mb_strlen($message);
    }

    /**
     * Get the number of SMS pages required for a message.
     *
     * Arabic messages use 70 characters per page, Latin messages use 160.
     *
     * @param string $message
     * @return int
     */
    public function getPageCount(string $message): int
    {
        $length = mb_strlen($message);
        if ($length === 0) {
            return 0;
        }

        $charsPerPage = $this->isArabic($message) ? 70 : 160;
        return (int) ceil($length / $charsPerPage);
    }

    /**
     * Check if a message contains Arabic characters.
     *
     * @param string $message
     * @return bool
     */
    public function isArabic(string $message): bool
    {
        return (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $message);
    }
}
