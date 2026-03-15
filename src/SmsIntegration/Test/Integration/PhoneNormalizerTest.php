<?php
/**
 * Phone Normalizer Tests.
 *
 * Tests phone normalization, validation, Arabic digit conversion,
 * trunk prefix stripping, coverage checks, and deduplication.
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Test\Integration;

use KwtSms\SmsIntegration\Model\Phone\Normalizer;
use KwtSms\SmsIntegration\Model\Phone\Rules;
use PHPUnit\Framework\TestCase;

class PhoneNormalizerTest extends TestCase
{
    private Normalizer $normalizer;

    protected function setUp(): void
    {
        // Normalizer without Magento DI (Config not needed for direct normalize calls)
        $this->normalizer = new Normalizer();
    }

    // ---- Kuwait numbers ----

    public function testKuwaitFullNumber(): void
    {
        $r = $this->normalizer->normalize('96598765432');
        $this->assertTrue($r->isValid());
        $this->assertEquals('96598765432', $r->getNormalized());
    }

    public function testKuwaitWithPlus(): void
    {
        $r = $this->normalizer->normalize('+96598765432');
        $this->assertTrue($r->isValid());
        $this->assertEquals('96598765432', $r->getNormalized());
    }

    public function testKuwaitWith00Prefix(): void
    {
        $r = $this->normalizer->normalize('0096598765432');
        $this->assertTrue($r->isValid());
        $this->assertEquals('96598765432', $r->getNormalized());
    }

    public function testKuwaitLocalNumber(): void
    {
        $r = $this->normalizer->normalize('98765432', '965');
        $this->assertTrue($r->isValid());
        $this->assertEquals('96598765432', $r->getNormalized());
    }

    public function testKuwaitWithSpaces(): void
    {
        $r = $this->normalizer->normalize('965 9876 5432');
        $this->assertTrue($r->isValid());
        $this->assertEquals('96598765432', $r->getNormalized());
    }

    public function testKuwaitWithDashes(): void
    {
        $r = $this->normalizer->normalize('965-9876-5432');
        $this->assertTrue($r->isValid());
        $this->assertEquals('96598765432', $r->getNormalized());
    }

    public function testKuwaitLandlineRejected(): void
    {
        $r = $this->normalizer->normalize('96522334455');
        $this->assertFalse($r->isValid());
        $this->assertStringContainsString('must start with', $r->getError());
    }

    // ---- Saudi numbers (trunk prefix stripping) ----

    public function testSaudiWithTrunkPrefix(): void
    {
        $r = $this->normalizer->normalize('9660559123456');
        $this->assertTrue($r->isValid());
        $this->assertEquals('966559123456', $r->getNormalized());
    }

    public function testSaudiNormal(): void
    {
        $r = $this->normalizer->normalize('966559123456');
        $this->assertTrue($r->isValid());
        $this->assertEquals('966559123456', $r->getNormalized());
    }

    public function testSaudiWithPlusAndTrunk(): void
    {
        $r = $this->normalizer->normalize('+966 0559123456');
        $this->assertTrue($r->isValid());
        $this->assertEquals('966559123456', $r->getNormalized());
    }

    // ---- UAE trunk prefix ----

    public function testUaeWithTrunkPrefix(): void
    {
        $r = $this->normalizer->normalize('9710501234567');
        $this->assertTrue($r->isValid());
        $this->assertEquals('971501234567', $r->getNormalized());
    }

    // ---- Egypt trunk prefix ----

    public function testEgyptWithTrunkPrefix(): void
    {
        // Egypt: country code 20, local 10 digits starting with 1
        // Input: 20 + 0 (trunk) + 1012345678 = 200 + 1012345678
        $r = $this->normalizer->normalize('2001012345678');
        $this->assertTrue($r->isValid(), $r->getError() ?? '');
        $this->assertEquals('201012345678', $r->getNormalized());
    }

    // ---- Arabic digits ----

    public function testArabicIndicDigits(): void
    {
        $r = $this->normalizer->normalize('٩٦٥٩٨٧٦٥٤٣٢');
        $this->assertTrue($r->isValid());
        $this->assertEquals('96598765432', $r->getNormalized());
    }

    public function testExtendedArabicIndicDigits(): void
    {
        $r = $this->normalizer->normalize('۹۶۵۹۸۷۶۵۴۳۲');
        $this->assertTrue($r->isValid());
        $this->assertEquals('96598765432', $r->getNormalized());
    }

    // ---- Invalid inputs ----

    public function testEmptyNumber(): void
    {
        $r = $this->normalizer->normalize('');
        $this->assertFalse($r->isValid());
        $this->assertNotEmpty($r->getError());
    }

    public function testTooShort(): void
    {
        $r = $this->normalizer->normalize('12345');
        $this->assertFalse($r->isValid());
        $this->assertNotEmpty($r->getError());
    }

    public function testTooLong(): void
    {
        $r = $this->normalizer->normalize('1234567890123456');
        $this->assertFalse($r->isValid());
        $this->assertStringContainsString('long', $r->getError());
    }

    public function testNoDigits(): void
    {
        $r = $this->normalizer->normalize('hello world');
        $this->assertFalse($r->isValid());
    }

    // ---- Mask ----

    public function testMaskPhone(): void
    {
        $masked = $this->normalizer->maskPhone('96598765432');
        $this->assertEquals('9659****432', $masked);
    }

    // ---- Deduplicate ----

    public function testDeduplicate(): void
    {
        $numbers = ['96598765432', '96591234567', '96598765432', '96591234567', '96599999999'];
        $result = $this->normalizer->deduplicate($numbers);
        $this->assertCount(3, $result);
    }

    // ---- Coverage ----

    public function testInCoverage(): void
    {
        $this->assertTrue($this->normalizer->isInCoverage('96598765432', ['965', '966']));
    }

    public function testNotInCoverage(): void
    {
        $this->assertFalse($this->normalizer->isInCoverage('971501234567', ['965']));
    }

    // ---- Country code detection ----

    public function testFindCountryCode3Digit(): void
    {
        $this->assertEquals('965', $this->normalizer->findCountryCode('96598765432'));
    }

    public function testFindCountryCode2Digit(): void
    {
        $this->assertEquals('20', $this->normalizer->findCountryCode('20101234567'));
    }

    public function testFindCountryCode1Digit(): void
    {
        $this->assertEquals('1', $this->normalizer->findCountryCode('12025551234'));
    }

    // ---- Rules table completeness ----

    public function testPhoneRulesHasGccCountries(): void
    {
        $rules = Rules::PHONE_RULES;
        $this->assertArrayHasKey('965', $rules, 'Kuwait missing');
        $this->assertArrayHasKey('966', $rules, 'Saudi missing');
        $this->assertArrayHasKey('971', $rules, 'UAE missing');
        $this->assertArrayHasKey('973', $rules, 'Bahrain missing');
        $this->assertArrayHasKey('974', $rules, 'Qatar missing');
        $this->assertArrayHasKey('968', $rules, 'Oman missing');
    }

    public function testCountryNamesHasGccCountries(): void
    {
        $names = Rules::COUNTRY_NAMES;
        $this->assertEquals('Kuwait', $names['965']);
        $this->assertEquals('Saudi Arabia', $names['966']);
        $this->assertEquals('UAE', $names['971']);
    }
}
