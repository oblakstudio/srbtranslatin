<?php
/**
 * PermalinkServiceTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use PHPUnit\Framework\TestCase;
use STL\Translit\Services\Permalink_Service;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test permalink transliteration parity behavior.
 */
final class PermalinkServiceTest extends TestCase {
    public function test_sanitize_title_transliterates_cyrillic_when_enabled_for_non_serbian_locale(): void {
        $service = new Permalink_Service(true, static fn(): string => 'en_US');

        self::assertSame('Ćirilica Đaković', $service->sanitize_title('Ћирилица Ђаковић'));
    }

    public function test_sanitize_title_leaves_title_unchanged_when_disabled(): void {
        $service = new Permalink_Service(false, static fn(): string => 'en_US');

        self::assertSame('Ћирилица', $service->sanitize_title('Ћирилица'));
    }

    public function test_sanitize_title_leaves_title_unchanged_for_serbian_locale(): void {
        $service = new Permalink_Service(true, static fn(): string => 'sr_RS');

        self::assertSame('Ћирилица', $service->sanitize_title('Ћирилица'));
    }

    public function test_sanitize_title_leaves_title_unchanged_for_bosnian_locale(): void {
        $service = new Permalink_Service(true, static fn(): string => 'bs_BA');

        self::assertSame('Ћирилица', $service->sanitize_title('Ћирилица'));
    }
}
