<?php
/**
 * Message Cleaner Tests.
 *
 * Tests emoji stripping, HTML removal, hidden char cleanup,
 * Arabic detection, character count, and page count.
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Test\Integration;

use KwtSms\SmsIntegration\Model\MessageCleaner;
use PHPUnit\Framework\TestCase;

class MessageCleanerTest extends TestCase
{
    private MessageCleaner $cleaner;

    protected function setUp(): void
    {
        $this->cleaner = new MessageCleaner();
    }

    public function testCleanPlainEnglish(): void
    {
        $this->assertEquals('Hello World', $this->cleaner->clean('Hello World'));
    }

    public function testCleanPlainArabic(): void
    {
        $msg = 'مرحبا بالعالم';
        $this->assertEquals($msg, $this->cleaner->clean($msg));
    }

    public function testStripEmoji(): void
    {
        $result = $this->cleaner->clean('Hello 😀🎉 World');
        $this->assertStringNotContainsString('😀', $result);
        $this->assertStringNotContainsString('🎉', $result);
        $this->assertStringContainsString('Hello', $result);
        $this->assertStringContainsString('World', $result);
    }

    public function testStripHtmlTags(): void
    {
        $result = $this->cleaner->clean('<b>Bold</b> <a href="#">Link</a>');
        $this->assertStringNotContainsString('<b>', $result);
        $this->assertStringNotContainsString('<a', $result);
        $this->assertStringContainsString('Bold', $result);
        $this->assertStringContainsString('Link', $result);
    }

    public function testStripZeroWidthChars(): void
    {
        $result = $this->cleaner->clean("Hello\xE2\x80\x8BWorld");
        $this->assertEquals('HelloWorld', $result);
    }

    public function testCollapseWhitespace(): void
    {
        $result = $this->cleaner->clean("Hello   \n\n   World");
        $this->assertStringNotContainsString('   ', $result);
    }

    public function testEmptyAfterClean(): void
    {
        $result = $this->cleaner->clean('😀🎉💯');
        $this->assertEquals('', trim($result));
    }

    public function testIsArabic(): void
    {
        $this->assertTrue($this->cleaner->isArabic('مرحبا'));
        $this->assertFalse($this->cleaner->isArabic('Hello'));
    }

    public function testPageCountEnglish(): void
    {
        $msg = str_repeat('A', 160);
        $this->assertEquals(1, $this->cleaner->getPageCount($msg));

        $msg = str_repeat('A', 161);
        $this->assertEquals(2, $this->cleaner->getPageCount($msg));
    }

    public function testPageCountArabic(): void
    {
        $msg = str_repeat('م', 70);
        $this->assertEquals(1, $this->cleaner->getPageCount($msg));

        $msg = str_repeat('م', 71);
        $this->assertEquals(2, $this->cleaner->getPageCount($msg));
    }

    public function testCharacterCount(): void
    {
        $this->assertEquals(5, $this->cleaner->getCharacterCount('Hello'));
        $this->assertEquals(5, $this->cleaner->getCharacterCount('مرحبا'));
    }
}
