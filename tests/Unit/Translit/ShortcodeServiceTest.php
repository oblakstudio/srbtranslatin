<?php
/**
 * ShortcodeServiceTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use PHPUnit\Framework\TestCase;
use STL\Translit\Contracts\Resolves_Language;
use STL\Translit\Services\Script_Manager;
use STL\Translit\Services\Shortcode_Service;
use STL\Translit\Services\Translit_Service;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test shortcode placeholder and rendering behavior.
 */
final class ShortcodeServiceTest extends TestCase {
    protected function setUp(): void {
        $GLOBALS['stl_test_registered_shortcodes'] = [];
        $GLOBALS['stl_test_uuid_counter'] = 0;
    }

    /**
     * @return void
     */
    public function test_register_shortcodes_registers_legacy_tags(): void {
        $service = new Shortcode_Service(
            $this->create_manager('lat', 'sr_RS'),
            static function (string $tag, callable $callback): void {
                $GLOBALS['stl_test_registered_shortcodes'][$tag] = $callback;
            },
            static fn (): string => 'stl-test-uuid-1'
        );

        $service->register_shortcodes();

        self::assertArrayHasKey('stl_cyr', $GLOBALS['stl_test_registered_shortcodes']);
        self::assertArrayHasKey('stl_cyrillic', $GLOBALS['stl_test_registered_shortcodes']);
        self::assertArrayHasKey('stl_translit', $GLOBALS['stl_test_registered_shortcodes']);
        self::assertArrayHasKey('stl_selective_output', $GLOBALS['stl_test_registered_shortcodes']);
        self::assertArrayHasKey('stl_show', $GLOBALS['stl_test_registered_shortcodes']);
    }

    /**
     * @return void
     */
    public function test_render_cyrillic_shortcode_protects_content_on_serbian_latin_pages(): void {
        $service = new Shortcode_Service(
            $this->create_manager('lat', 'sr_RS'),
            null,
            static fn (): string => 'stl-test-uuid-1'
        );

        $token = $service->render_cyrillic_shortcode(array(), 'Ћирилица');

        self::assertSame('stl-test-uuid-1', $token);
        self::assertSame('Ћирилица', $service->restore_placeholders($token));
    }

    /**
     * @return void
     */
    public function test_render_translit_shortcode_uses_explicit_latin_replacement_on_serbian_latin_pages(): void {
        $service = new Shortcode_Service(
            $this->create_manager('lat', 'sr_RS'),
            null,
            static fn (): string => 'stl-test-uuid-1'
        );

        $token = $service->render_translit_shortcode(array('latin' => 'Custom Latin'), 'Ћирилица', 'stl_translit');

        self::assertSame('Custom Latin', $service->restore_placeholders($token));
    }

    /**
     * @return void
     */
    public function test_render_selective_output_shortcode_only_renders_matching_script(): void {
        $service = new Shortcode_Service($this->create_manager('lat', 'sr_RS'));

        self::assertSame('Latin only', $service->render_selective_output_shortcode(array('script' => 'lat'), 'Latin only'));
        self::assertSame('', $service->render_selective_output_shortcode(array('script' => 'cir'), 'Cyrillic only'));
    }

    /**
     * @return void
     */
    public function test_buffer_end_restores_protected_shortcodes_after_transliteration(): void {
        $manager = $this->create_manager('lat', 'sr_RS');
        $shortcodes = new Shortcode_Service(
            $manager,
            null,
            static fn (): string => 'stl-test-uuid-1'
        );
        $service = new Translit_Service($manager, null, $shortcodes);

        $token = $shortcodes->render_cyrillic_shortcode(array(), 'Ћирилица');
        $result = $service->buffer_end('Љубав ' . $token);

        self::assertSame('Ljubav Ћирилица', $result);
    }

    private function create_manager(string $script, string $language): Script_Manager {
        $manager = new Script_Manager(
            'both',
            'cir',
            $script,
            '',
            'pismo',
            new FixedShortcodeLanguageResolver($language)
        );
        $manager->initialize();

        return $manager;
    }
}

final class FixedShortcodeLanguageResolver implements Resolves_Language {
    public function __construct(private ?string $language) {
    }

    public function resolve_language(): ?string {
        return $this->language;
    }
}
