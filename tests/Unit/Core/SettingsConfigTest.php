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

    public function test_disables_permalink_fix_for_serbian_locales(): void {
        $GLOBALS['stl_test_locale'] = 'sr_RS';

        $schema = require dirname(__DIR__, 3) . '/config/settings.php';
        $field = $this->findField($schema['fields'], 'fix_permalinks');

        self::assertTrue($field['extras']['html_attributes']['disabled']);
        self::assertStringContainsString('sr_RS', (string) $field['extras']['description']);
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
