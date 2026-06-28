<?php
/**
 * WPMLServiceTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\ML;

use PHPUnit\Framework\TestCase;
use STL\Common\Settings\Array_Settings;
use STL\ML\Services\WPML_Service;
use STL\Translit\Contracts\Resolves_Language;
use STL\Translit\Services\Script_Manager;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test WPML language switcher integration.
 */
final class WPMLServiceTest extends TestCase {
    /**
     * @return void
     */
    public function test_extend_language_selector_splits_serbian_into_cyrillic_and_latin_entries(): void {
        $service = new WPML_Service(
            $this->create_manager('lat'),
            new Array_Settings(array('extend_ls' => true)),
            static fn (string $key, string $value, string $url): string => $url . '?' . $key . '=' . $value,
            static fn (string $content): string => $content
        );

        $result = $service->extend_language_selector(
            array(
                'sr' => array(
                    'native_name' => 'srpski',
                    'translated_name' => 'Serbian',
                    'url' => '/sr',
                    'active' => false,
                ),
            )
        );

        self::assertArrayHasKey('sr', $result);
        self::assertArrayHasKey('sr@lat', $result);
        self::assertSame('/sr?pismo=cir', $result['sr']['url']);
        self::assertSame('/sr?pismo=lat', $result['sr@lat']['url']);
        self::assertFalse($result['sr']['active']);
        self::assertTrue($result['sr@lat']['active']);
    }

    /**
     * @return void
     */
    public function test_extend_language_selector_skips_when_setting_is_disabled(): void {
        $service = new WPML_Service(
            $this->create_manager('cir'),
            new Array_Settings(array('extend_ls' => false))
        );
        $languages = array(
            'sr' => array(
                'native_name' => 'srpski',
                'translated_name' => 'Serbian',
                'url' => '/sr',
                'active' => true,
            ),
        );

        self::assertSame($languages, $service->extend_language_selector($languages));
    }

    /**
     * @return void
     */
    public function test_extend_language_selector_skips_when_serbian_is_not_primary_language(): void {
        $service = new WPML_Service(
            $this->create_manager('cir'),
            new Array_Settings(array('extend_ls' => true))
        );
        $languages = array(
            'en' => array(
                'native_name' => 'English',
                'translated_name' => 'English',
                'url' => '/en',
                'active' => true,
            ),
        );

        self::assertSame($languages, $service->extend_language_selector($languages));
    }

    private function create_manager(string $script): Script_Manager {
        $manager = new Script_Manager(
            'both',
            'cir',
            $script,
            '',
            'pismo',
            new FixedWpmlLanguageResolver('sr_RS')
        );
        $manager->initialize();

        return $manager;
    }
}

final class FixedWpmlLanguageResolver implements Resolves_Language {
    public function __construct(private ?string $language) {
    }

    public function resolve_language(): ?string {
        return $this->language;
    }
}
