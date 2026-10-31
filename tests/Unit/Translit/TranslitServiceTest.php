<?php
/**
 * TranslitServiceTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use PHPUnit\Framework\TestCase;
use STL\Translit\Services\Script_Manager;
use STL\Translit\Services\Translit_Service;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test transliteration service behavior.
 */
final class TranslitServiceTest extends TestCase {
    public function test_legacy_image_variant_links_change_only_image_attributes(): void {
        $service = new Translit_Service($this->create_manager('lat'));
        $result = $service->change_image_urls('<p>file__cir.jpg</p><img src="file__cir.jpg" srcset="file__cir.jpg 1x, file__cir-2.jpg 2x" alt="file__cir">');
        self::assertStringContainsString('<p>file__cir.jpg</p>', $result);
        self::assertStringContainsString('src="file__lat.jpg"', $result);
        self::assertStringContainsString('srcset="file__lat.jpg 1x, file__lat-2.jpg 2x"', $result);
        self::assertStringContainsString('alt="file__cir"', $result);
    }

    public function test_image_links_are_preserved_for_cyrillic_and_ordinary_content(): void {
        $html = '<img src="file__cir.jpg">';
        self::assertSame($html, (new Translit_Service($this->create_manager('cir')))->change_image_urls($html));
        $ordinary = '<img src="normal.jpg">';
        self::assertSame($ordinary, (new Translit_Service($this->create_manager('lat')))->change_image_urls($ordinary));
    }

    /**
     * @return void
     */
    public function test_buffer_end_transliterates_frontend_output_when_latin_is_active(): void {
        $manager = $this->create_manager('lat');
        $service = new Translit_Service($manager);

        self::assertSame('Ćirilica', $service->buffer_end('Ћирилица'));
    }

    /**
     * @return void
     */
    public function test_gettext_transliterates_strings_when_latin_is_active(): void {
        $manager = $this->create_manager('lat');
        $service = new Translit_Service($manager);

        self::assertSame('Ćirilica', $service->translate_gettext('Ћирилица'));
    }

    /**
     * @return void
     */
    public function test_buffer_end_returns_contents_unchanged_when_transliteration_is_disabled(): void {
        $manager = $this->create_manager('cir');
        $service = new Translit_Service($manager);

        self::assertSame('Ћирилица', $service->buffer_end('Ћирилица'));
    }

    /**
     * @return void
     */
    public function test_ajax_buffer_end_transliterates_plain_string_when_latin_is_active(): void {
        $manager = $this->create_manager('lat');
        $service = new Translit_Service($manager);

        self::assertSame('Ćirilica', $service->ajax_buffer_end('Ћирилица'));
    }

    /**
     * @return void
     */
    public function test_ajax_buffer_end_transliterates_nested_json_string_values(): void {
        $manager = $this->create_manager('lat');
        $service = new Translit_Service($manager);

        $contents = wp_json_encode(
            array(
                'title'  => 'Ћирилица',
                'nested' => array(
                    'summary' => 'Љубав',
                    'count'   => 3,
                    'active'  => true,
                ),
            )
        );

        self::assertSame(
            '{"title":"Ćirilica","nested":{"summary":"Ljubav","count":3,"active":true}}',
            $service->ajax_buffer_end((string) $contents)
        );
    }

    /**
     * @return void
     */
    public function test_ajax_buffer_end_decodes_html_entities_inside_json_string_values(): void {
        $manager = $this->create_manager('lat');
        $service = new Translit_Service($manager);

        $contents = wp_json_encode(
            array(
                'title' => '&#1035;ирилица',
            )
        );

        self::assertSame(
            '{"title":"Ćirilica"}',
            $service->ajax_buffer_end((string) $contents)
        );
    }

    /**
     * @return void
     */
    public function test_ajax_buffer_end_falls_back_to_plain_text_transliteration_for_invalid_json(): void {
        $manager = $this->create_manager('lat');
        $service = new Translit_Service($manager);

        self::assertSame('Ćirilica{', $service->ajax_buffer_end('Ћирилица{'));
    }

    private function create_manager(string $script, string $locale = 'sr_RS'): Script_Manager {
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
