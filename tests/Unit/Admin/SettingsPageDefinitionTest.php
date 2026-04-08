<?php

declare(strict_types=1);

namespace STL\Tests\Unit\Admin;

use PHPUnit\Framework\TestCase;
use STL\Admin\Settings\Admin_Context;
use STL\Admin\Settings\Definitions\Advanced_Tab_Definition;
use STL\Admin\Settings\Definitions\General_Tab_Definition;
use STL\Admin\Settings\Definitions\Media_Tab_Definition;
use STL\Admin\Settings\Definitions\Menu_Tab_Definition;
use STL\Admin\Settings\Settings_Page_Definition;

final class SettingsPageDefinitionTest extends TestCase {
    public function test_builds_core_tabs_and_fields(): void {
        $definition = new Settings_Page_Definition(
            [
                new General_Tab_Definition(),
                new Menu_Tab_Definition(new Fake_Admin_Context('en_US', ['primary' => 'Primary menu'])),
                new Media_Tab_Definition(),
                new Advanced_Tab_Definition(new Fake_Admin_Context('en_US')),
            ],
        );

        $schema = $definition->build();

        self::assertSame(['general', 'menu', 'media', 'advanced'], array_column($schema['tabs'], 'id'));
        self::assertContains('default_script', array_column($schema['fields'], 'id'));
        self::assertContains('extend_menu', array_column($schema['fields'], 'id'));
        self::assertContains('transliterate_uploads', array_column($schema['fields'], 'id'));
        self::assertContains('fix_titles', array_column($schema['fields'], 'id'));
    }

    public function test_disables_menu_fields_when_no_registered_menus_exist(): void {
        $definition = new Menu_Tab_Definition(new Fake_Admin_Context('en_US', []));

        $schema = $definition->build();
        $fields = [];

        foreach ($schema['fields'] as $field) {
            $fields[$field['id']] = $field;
        }

        self::assertTrue($fields['extend']['extras']['html_attributes']['disabled']);
        self::assertTrue($fields['extend_menu']['extras']['html_attributes']['disabled']);
        self::assertArrayHasKey('menu_warning', $fields);
    }

    public function test_disables_permalink_fix_for_serbian_locales(): void {
        $definition = new Advanced_Tab_Definition(new Fake_Admin_Context('sr_RS'));

        $schema = $definition->build();
        $fields = [];

        foreach ($schema['fields'] as $field) {
            $fields[$field['id']] = $field;
        }

        self::assertTrue($fields['fix_permalinks']['extras']['html_attributes']['disabled']);
        self::assertStringContainsString('sr_RS', (string) $fields['fix_permalinks']['extras']['description']);
    }
}

final class Fake_Admin_Context implements Admin_Context {
    public function __construct(
        private string $locale,
        private array $nav_menus = [],
    ) {
    }

    public function get_locale(): string {
        return $this->locale;
    }

    public function get_registered_nav_menus(): array {
        return $this->nav_menus;
    }
}
