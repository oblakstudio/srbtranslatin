<?php
/**
 * MediaServiceTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use PHPUnit\Framework\TestCase;
use STL\Common\Settings\Array_Settings;
use STL\Translit\Services\Media_Service;
use STL\Translit\Services\Script_Manager;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test media transliteration behavior.
 */
final class MediaServiceTest extends TestCase {
    /**
     * @return void
     */
    public function test_transliterates_upload_filename_to_cut_latin_when_enabled(): void {
        $service = $this->create_service(
            array(
                'transliterate_uploads' => true,
            )
        );

        self::assertSame('cirilica.jpg', $service->transliterate_filename('ћирилица.jpg'));
    }

    /**
     * @return void
     */
    public function test_leaves_upload_filename_unchanged_when_transliteration_is_disabled(): void {
        $service = $this->create_service(
            array(
                'transliterate_uploads' => false,
            )
        );

        self::assertSame('ћирилица.jpg', $service->transliterate_filename('ћирилица.jpg'));
    }

    /**
     * @return void
     */
    public function test_rewrites_image_src_and_srcset_markers_on_latin_pages(): void {
        $service = $this->create_service(
            array(
                'separate_uploads' => true,
            ),
            'lat'
        );

        $html = '<p><img src="/uploads/photo-cir.jpg" srcset="/uploads/photo-cir.jpg 1x, /uploads/photo-cir@2x.jpg 2x" alt=""></p>';

        self::assertSame(
            '<p><img src="/uploads/photo-lat.jpg" srcset="/uploads/photo-lat.jpg 1x, /uploads/photo-lat@2x.jpg 2x" alt=""></p>',
            $service->rewrite_image_urls($html)
        );
    }

    /**
     * @return void
     */
    public function test_rewrites_image_urls_with_custom_separator(): void {
        $service = $this->create_service(
            array(
                'separate_uploads'   => true,
                'filename_separator' => '__',
            ),
            'lat'
        );

        $html = '<img src="/uploads/photo__cir.jpg" srcset="/uploads/photo__cir.jpg 640w">';

        self::assertSame(
            '<img src="/uploads/photo__lat.jpg" srcset="/uploads/photo__lat.jpg 640w">',
            $service->rewrite_image_urls($html)
        );
    }

    /**
     * @dataProvider unchanged_image_html_provider
     *
     * @param array<string,mixed> $settings Media settings.
     */
    public function test_leaves_image_html_unchanged_when_rewrite_conditions_are_not_met(
        array $settings,
        string $script,
        string $locale,
        string $html
    ): void {
        $service = $this->create_service($settings, $script, $locale);

        self::assertSame($html, $service->rewrite_image_urls($html));
    }

    /**
     * @return array<string,array{0:array<string,mixed>,1:string,2:string,3:string}>
     */
    public static function unchanged_image_html_provider(): array {
        return array(
            'separate uploads disabled' => array(
                array( 'separate_uploads' => false ),
                'lat',
                'sr_RS',
                '<img src="/uploads/photo-cir.jpg">',
            ),
            'cyrillic active script'     => array(
                array( 'separate_uploads' => true ),
                'cir',
                'sr_RS',
                '<img src="/uploads/photo-cir.jpg">',
            ),
            'unsupported locale'         => array(
                array( 'separate_uploads' => true ),
                'lat',
                'en_US',
                '<img src="/uploads/photo-cir.jpg">',
            ),
            'missing marker'             => array(
                array( 'separate_uploads' => true ),
                'lat',
                'sr_RS',
                '<img src="/uploads/photo.jpg">',
            ),
        );
    }

    /**
     * @param array<string,mixed> $settings Media settings.
     */
    private function create_service(array $settings, string $script = 'lat', string $locale = 'sr_RS'): Media_Service {
        return new Media_Service(
            $this->create_manager($script, $locale),
            new Array_Settings($settings)
        );
    }

    private function create_manager(string $script, string $locale): Script_Manager {
        $manager = new Script_Manager(
            'both',
            'cir',
            $script,
            '',
            'pismo',
            null,
            null,
            static fn(): string => $locale
        );
        $manager->initialize();

        return $manager;
    }
}
