<?php
/**
 * TitleHandlerTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use PHPUnit\Framework\TestCase;
use STL\Common\Settings\Array_Settings;
use STL\Translit\Handlers\Title_Handler;
use STL\Translit\Services\Script_Manager;
use STL\Translit\Services\Translit_Service;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test title transliteration behavior.
 */
final class TitleHandlerTest extends TestCase {
    protected function setUp(): void {
        $GLOBALS['stl_test_theme_supports'] = [];

        if ( \function_exists( 'remove_theme_support' ) ) {
            \remove_theme_support( 'title-tag' );
        }
    }

    /**
     * @return void
     */
    public function test_transliterate_title_converts_classic_title_when_enabled_and_latin_is_active(): void {
        $handler = $this->create_handler('lat', true);

        self::assertSame('Ćirilica', $handler->transliterate_title('Ћирилица'));
    }

    /**
     * @return void
     */
    public function test_transliterate_title_skips_when_title_tag_is_supported(): void {
        $GLOBALS['stl_test_theme_supports']['title-tag'] = true;
        $handler = $this->create_handler('lat', true);

        self::assertSame('Ћирилица', $handler->transliterate_title('Ћирилица'));
    }

    /**
     * @return void
     */
    public function test_transliterate_title_parts_converts_each_part_when_enabled_and_latin_is_active(): void {
        $handler = $this->create_handler('lat', true);

        self::assertSame(
            array(
                'title' => 'Ćirilica',
                'site'  => 'Ljubav',
                'page'  => 2,
            ),
            $handler->transliterate_title_parts(
                array(
                    'title' => 'Ћирилица',
                    'site'  => 'Љубав',
                    'page'  => 2,
                )
            )
        );
    }

    /**
     * @return void
     */
    public function test_transliterate_title_parts_skip_when_disabled(): void {
        $handler = $this->create_handler('lat', false);

        self::assertSame(
            array(
                'title' => 'Ћирилица',
                'site'  => 'Љубав',
            ),
            $handler->transliterate_title_parts(
                array(
                    'title' => 'Ћирилица',
                    'site'  => 'Љубав',
                )
            )
        );
    }

    /**
     * @return void
     */
    public function test_transliterate_title_parts_skip_when_cyrillic_is_active(): void {
        $handler = $this->create_handler('cir', true);

        self::assertSame(
            array(
                'title' => 'Ћирилица',
            ),
            $handler->transliterate_title_parts(
                array(
                    'title' => 'Ћирилица',
                )
            )
        );
    }

    /**
     * @return void
     */
    public function test_transliterate_title_skips_when_latin_is_active_for_unsupported_locale(): void {
        $handler = $this->create_handler('lat', true, 'en_US');

        self::assertSame('Ћирилица', $handler->transliterate_title('Ћирилица'));
    }

    /**
     * @return void
     */
    public function test_transliterate_title_parts_skip_when_latin_is_active_for_unsupported_locale(): void {
        $handler = $this->create_handler('lat', true, 'en_US');

        self::assertSame(
            array(
                'title' => 'Ћирилица',
            ),
            $handler->transliterate_title_parts(
                array(
                    'title' => 'Ћирилица',
                )
            )
        );
    }

    private function create_handler(string $script, bool $fix_titles, string $locale = 'sr_RS'): Title_Handler {
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

        $service = new Translit_Service($manager);
        $settings = new Array_Settings(
            array(
                'fix_titles' => $fix_titles,
            )
        );

        return new Title_Handler(
            $service,
            $manager,
            $settings,
            static fn (string $feature): bool => (bool) ($GLOBALS['stl_test_theme_supports'][$feature] ?? false)
        );
    }
}
