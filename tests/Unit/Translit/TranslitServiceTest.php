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
