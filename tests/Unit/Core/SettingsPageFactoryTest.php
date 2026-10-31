<?php

declare(strict_types=1);

namespace STL\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use STL\Core\Services\Settings_Page;
use STL\Core\Services\Settings_Page_Factory;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

final class SettingsPageFactoryTest extends TestCase {
    protected function setUp(): void {
        $this->save(['url_param' => 'pismo', 'fix_search' => true, 'fix_titles' => true, 'transliterate_uploads' => true]);
        $GLOBALS['stl_test_settings_errors'] = [];
    }

    protected function tearDown(): void {
        if (function_exists('delete_option')) {
            delete_option('stl_settings_test');
        }
        unset($GLOBALS['stl_test_options']['stl_settings_test']);
        parent::tearDown();
    }

    public function test_factory_returns_native_settings_page(): void {
        self::assertInstanceOf(Settings_Page::class, $this->page());
    }

    public function test_save_preserves_fields_from_other_tabs_and_rejects_unknown_keys(): void {
        $values = $this->page()->sanitize(['url_param' => 'script', 'unexpected' => 'injected']);
        self::assertSame('script', $values['url_param']);
        self::assertTrue($values['fix_search']);
        self::assertTrue($values['fix_titles']);
        self::assertArrayNotHasKey('unexpected', $values);
    }

    public function test_unchecked_checkbox_is_saved_as_false(): void {
        self::assertFalse($this->page()->sanitize(['fix_search' => '0'])['fix_search']);
    }

    public function test_invalid_choice_and_nested_input_preserve_previous_values(): void {
        $this->save(['url_param' => 'pismo', 'default_script' => 'lat']);
        $values = $this->page()->sanitize(['url_param' => ['nested'], 'default_script' => 'invalid']);
        self::assertSame('pismo', $values['url_param']);
        self::assertSame('lat', $values['default_script']);
    }

    public function test_invalid_url_parameter_is_rejected_without_changing_it(): void {
        self::assertSame('pismo', $this->page()->sanitize(['url_param' => 'bad key[]'])['url_param']);
    }

    public function test_disabled_fields_cannot_be_changed_by_crafted_submission(): void {
        self::assertTrue($this->page()->sanitize(['transliterate_uploads' => '0'])['transliterate_uploads']);
    }

    /** @param array<string,mixed> $values */
    private function save(array $values): void {
        if (function_exists('update_option')) {
            update_option('stl_settings_test', $values);
        }
        $GLOBALS['stl_test_options']['stl_settings_test'] = $values;
    }

    private function page(): Settings_Page {
        return Settings_Page_Factory::make([
            'page' => ['option_name' => 'stl_settings_test', 'slug' => 'stl-settings-test', 'page_title' => 'Settings', 'menu_title' => 'Settings', 'parent_slug' => 'options-general.php', 'capability' => 'manage_options'],
            'tabs' => [],
            'sections' => [],
            'fields' => [
                ['id' => 'url_param', 'type' => 'text'],
                ['id' => 'default_script', 'type' => 'select', 'extras' => ['options' => ['cir' => 'Cyrillic', 'lat' => 'Latin']]],
                ['id' => 'fix_search', 'type' => 'checkbox'],
                ['id' => 'transliterate_uploads', 'type' => 'checkbox', 'extras' => ['html_attributes' => ['disabled' => true]]],
            ],
        ]);
    }
}
