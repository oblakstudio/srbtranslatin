<?php
/**
 * MediaServiceTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use PHPUnit\Framework\TestCase;
use STL\Translit\Services\Media_Service;
use STL\Translit\Services\Script_Manager;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test media filename and URL parity behavior.
 */
final class MediaServiceTest extends TestCase {
    public function test_sanitize_file_name_transliterates_uploads_to_cut_latin_when_enabled(): void {
        $service = $this->create_service('lat');

        self::assertSame('Cirilica Djakovic.png', $service->sanitize_file_name('Ћирилица Ђаковић.png'));
    }

    public function test_sanitize_file_name_leaves_uploads_unchanged_when_disabled(): void {
        $service = $this->create_service('lat', transliterate_uploads: false);

        self::assertSame('Ћирилица Ђаковић.png', $service->sanitize_file_name('Ћирилица Ђаковић.png'));
    }

    public function test_rewrite_media_urls_switches_configured_separator_and_legacy_markers_to_latin(): void {
        $service = $this->create_service('lat', separator: '-');

        $html = '<img src="/uploads/slika-cir.jpg" srcset="/uploads/slika__cir-300.jpg 300w"><a href="/uploads/doc-cir.pdf">Документ</a>';

        self::assertSame(
            '<img src="/uploads/slika-lat.jpg" srcset="/uploads/slika__lat-300.jpg 300w"><a href="/uploads/doc-lat.pdf">Документ</a>',
            $service->rewrite_media_urls($html)
        );
    }

    public function test_rewrite_media_urls_switches_configured_separator_and_legacy_markers_to_cyrillic(): void {
        $service = $this->create_service('cir', separator: '-');

        $html = '<img src="/uploads/slika-lat.jpg" srcset="/uploads/slika__lat-300.jpg 300w"><a href="/uploads/doc-lat.pdf">Документ</a>';

        self::assertSame(
            '<img src="/uploads/slika-cir.jpg" srcset="/uploads/slika__cir-300.jpg 300w"><a href="/uploads/doc-cir.pdf">Документ</a>',
            $service->rewrite_media_urls($html)
        );
    }

    public function test_rewrite_media_urls_leaves_content_unchanged_when_separate_uploads_are_disabled(): void {
        $service = $this->create_service('lat', separate_uploads: false);

        self::assertSame('/uploads/slika__cir.jpg', $service->rewrite_media_urls('/uploads/slika__cir.jpg'));
    }

    public function test_rewrite_page_output_respects_website_transliteration_method(): void {
        $service = $this->create_service('lat', method: 'website');

        self::assertSame('/uploads/slika__lat.jpg', $service->rewrite_page_output('/uploads/slika__cir.jpg'));
    }

    public function test_rewrite_page_output_skips_content_only_transliteration_method(): void {
        $service = $this->create_service('lat', method: 'content');

        self::assertSame('/uploads/slika__cir.jpg', $service->rewrite_page_output('/uploads/slika__cir.jpg'));
    }

    public function test_rewrite_content_works_for_content_only_transliteration_method(): void {
        $service = $this->create_service('lat', method: 'content');

        self::assertSame('/uploads/slika__lat.jpg', $service->rewrite_content('/uploads/slika__cir.jpg'));
    }

    private function create_service(
        string $script,
        bool $transliterate_uploads = true,
        bool $separate_uploads = true,
        string $separator = '-',
        string $method = 'website'
    ): Media_Service {
        $manager = new Script_Manager(
            'both',
            'cir',
            $script,
            '',
            'pismo',
            null,
            null,
            static fn(): string => 'sr_RS'
        );
        $manager->initialize();

        return new Media_Service(
            $manager,
            $transliterate_uploads,
            $separate_uploads,
            $separator,
            $method
        );
    }
}
