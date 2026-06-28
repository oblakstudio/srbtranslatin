<?php

declare(strict_types=1);

namespace STL\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

final class SettingsConfigTest extends TestCase {
    protected function tearDown(): void {
        $GLOBALS['stl_test_locale'] = 'en_US';
        $GLOBALS['stl_test_nav_menus'] = [];

        parent::tearDown();
    }

    public function test_enables_permalink_fix_for_serbian_locales(): void {
        $GLOBALS['stl_test_locale'] = 'sr_RS';

        $schema = require dirname(__DIR__, 3) . '/config/settings.php';
        $field = $this->findField($schema['fields'], 'fix_permalinks');

        self::assertFalse($field['extras']['html_attributes']['disabled']);
        self::assertSame(false, $field['extras']['default']);
        self::assertStringContainsString('permalinks', (string) $field['extras']['description']);
    }

    public function test_enables_filename_transliteration_while_media_variant_support_is_deferred(): void {
        $schema = require dirname(__DIR__, 3) . '/config/settings.php';

        $warning = $this->findField($schema['fields'], 'media_warning');
        $transliterateUploads = $this->findField($schema['fields'], 'transliterate_uploads');
        $separateUploads = $this->findField($schema['fields'], 'separate_uploads');
        $separator = $this->findField($schema['fields'], 'filename_separator');
        $method = $this->findField($schema['fields'], 'transliteration_method');

        self::assertNotSame('', $warning['extras']['description']);
        self::assertStringNotContainsString('legacy settings', (string) $warning['extras']['description']);
        self::assertStringNotContainsString('not active in the current src runtime yet', (string) $warning['extras']['description']);
        self::assertFalse($transliterateUploads['extras']['html_attributes']['disabled']);
        self::assertStringContainsString('Script-specific media variants', (string) $warning['extras']['description']);
        self::assertTrue($separateUploads['extras']['html_attributes']['disabled']);
        self::assertTrue($separator['extras']['html_attributes']['disabled']);
        self::assertTrue($method['extras']['html_attributes']['disabled']);
    }

    public function test_disables_menu_fields_when_no_registered_menus_exist(): void {
        $schema = require dirname(__DIR__, 3) . '/config/settings.php';

        $extend = $this->findField($schema['fields'], 'extend');
        $extendMenu = $this->findField($schema['fields'], 'extend_menu');
        $warning = $this->findField($schema['fields'], 'menu_warning');

        self::assertTrue($extend['extras']['html_attributes']['disabled']);
        self::assertTrue($extendMenu['extras']['html_attributes']['disabled']);
        self::assertNotSame('', $warning['extras']['description']);
    }

    public function test_populates_registered_navigation_menus_in_schema(): void {
        $GLOBALS['stl_test_nav_menus'] = [
            'primary' => 'Primary menu',
        ];

        $schema = require dirname(__DIR__, 3) . '/config/settings.php';
        $field = $this->findField($schema['fields'], 'extend_menu');

        self::assertSame([
            '' => 'Select a menu',
            'primary' => 'Primary menu',
        ], $field['extras']['options']);
        self::assertFalse($field['extras']['html_attributes']['disabled']);
    }

    public function test_enabled_scripts_exposes_two_plugin_modes(): void {
        $schema = require dirname(__DIR__, 3) . '/config/settings.php';
        $field = $this->findField($schema['fields'], 'enabled_scripts');

        self::assertSame('buttons_group', $field['type']);
        self::assertSame('both', $field['extras']['default']);
        self::assertSame([
            'lat' => 'Ć Latin',
            'both' => 'Ć Ћ Cyrillic / Latin',
        ], $field['extras']['options']);
    }

    public function test_default_script_uses_button_group_and_only_shows_for_dual_mode(): void {
        $schema = require dirname(__DIR__, 3) . '/config/settings.php';
        $field = $this->findField($schema['fields'], 'default_script');

        self::assertSame('buttons_group', $field['type']);
        self::assertSame([
            'cir' => 'Ћ Cyrillic',
            'lat' => 'Ć Latin',
        ], $field['extras']['options']);
        self::assertSame('enabled_scripts', $field['extras']['conditions']['rules'][0]['field']);
        self::assertSame('both', $field['extras']['conditions']['rules'][0]['value']);
    }

    /**
     * @param array<int,array<string,mixed>> $fields
     * @return array<string,mixed>
     */
    private function findField(array $fields, string $id): array {
        foreach ($fields as $field) {
            if (($field['id'] ?? null) === $id) {
                return $field;
            }
        }

        self::fail(sprintf('Field "%s" was not found in schema.', $id));
    }
}
