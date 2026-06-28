<?php
/**
 * MediaPermalinkServiceTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use PHPUnit\Framework\TestCase;
use STL\Common\Settings\Array_Settings;
use STL\Translit\Services\Media_Permalink_Service;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test media filename and permalink transliteration.
 */
final class MediaPermalinkServiceTest extends TestCase {
    /**
     * @return void
     */
    public function test_transliterate_filename_converts_cyrillic_filename_when_enabled(): void {
        self::assertTrue(class_exists(Media_Permalink_Service::class));

        $service = new Media_Permalink_Service(new Array_Settings(['transliterate_uploads' => true]));

        self::assertSame('Cirilica Sabac.jpg', $service->transliterate_filename('Ћирилица Шабац.jpg'));
    }

    /**
     * @return void
     */
    public function test_transliterate_filename_returns_original_filename_when_disabled(): void {
        self::assertTrue(class_exists(Media_Permalink_Service::class));

        $service = new Media_Permalink_Service(new Array_Settings(['transliterate_uploads' => false]));

        self::assertSame('Ћирилица Шабац.jpg', $service->transliterate_filename('Ћирилица Шабац.jpg'));
    }

    /**
     * @return void
     */
    public function test_transliterate_permalink_slug_converts_cyrillic_title_on_save_when_enabled(): void {
        self::assertTrue(class_exists(Media_Permalink_Service::class));

        $service = new Media_Permalink_Service(new Array_Settings(['fix_permalinks' => true]));

        self::assertSame('Cirilica Sabac', $service->transliterate_permalink_slug('Ћирилица Шабац', 'Ћирилица Шабац', 'save'));
    }

    /**
     * @return void
     */
    public function test_transliterate_permalink_slug_returns_original_title_when_disabled(): void {
        self::assertTrue(class_exists(Media_Permalink_Service::class));

        $service = new Media_Permalink_Service(new Array_Settings(['fix_permalinks' => false]));

        self::assertSame('Ћирилица Шабац', $service->transliterate_permalink_slug('Ћирилица Шабац', 'Ћирилица Шабац', 'save'));
    }

    /**
     * @return void
     */
    public function test_transliterate_permalink_slug_ignores_non_save_context(): void {
        self::assertTrue(class_exists(Media_Permalink_Service::class));

        $service = new Media_Permalink_Service(new Array_Settings(['fix_permalinks' => true]));

        self::assertSame('Ћирилица Шабац', $service->transliterate_permalink_slug('Ћирилица Шабац', 'Ћирилица Шабац', 'query'));
    }
}
