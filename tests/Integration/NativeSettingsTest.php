<?php

declare(strict_types=1);

use STL\Common\Settings\Settings_Schema;
use STL\Common\Settings\WordPress_Option_Settings;
use STL\Core\Services\Settings_Page;

final class NativeSettingsTest extends WP_UnitTestCase {
    private Settings_Page $page;

    public function set_up(): void {
        parent::set_up();
        $GLOBALS['wp_settings_errors'] = [];
        $GLOBALS['stl_test_nav_menus'] = ['primary' => 'Primary'];
        delete_option(Settings_Schema::OPTION_NAME);
        foreach (array_keys(Settings_Schema::defaults()) as $group) {
            delete_option('srbtranslatin_' . $group);
        }
        $this->page = new Settings_Page(require STL_PATH . 'config/settings.php');
        $this->page->register_settings();
    }

    public function tear_down(): void {
        unregister_setting(Settings_Schema::PAGE_SLUG, Settings_Schema::OPTION_NAME);
        unset($_GET['tab']);
        $GLOBALS['stl_test_nav_menus'] = [];
        $GLOBALS['wp_settings_errors'] = [];
        parent::tear_down();
    }

    public function test_absent_legacy_options_keep_the_3_x_runtime_defaults(): void {
        $settings = new WordPress_Option_Settings();
        self::assertFalse($settings->get('advanced', 'fix_search'));
        self::assertTrue($settings->get('wpml', 'extend_ls'));
        self::assertSame('cir', $settings->get('general', 'default_script'));
        self::assertTrue($settings->get('media', 'transliterate_uploads'));
    }

    public function test_legacy_values_remain_available_after_registering_settings(): void {
        update_option('srbtranslatin_general', ['default_script' => 'lat', 'url_param' => 'script']);
        update_option('srbtranslatin_menu', ['extend' => 'no', 'menu_title' => 'Писмо']);
        update_option('srbtranslatin_wpml', ['extend_ls' => 'yes']);
        $settings = new WordPress_Option_Settings();
        self::assertSame('lat', $settings->get('general', 'default_script'));
        self::assertSame('script', $settings->get('general', 'url_param'));
        self::assertFalse($settings->get('menu', 'extend'));
        self::assertTrue($settings->get('wpml', 'extend_ls'));
        self::assertNull(get_option(Settings_Schema::OPTION_NAME, null));
    }

    public function test_first_save_migrates_other_tabs_without_changing_legacy_options(): void {
        update_option('srbtranslatin_general', ['default_script' => 'lat', 'url_param' => 'pismo']);
        update_option('srbtranslatin_advanced', ['fix_search' => 'no', 'fix_titles' => 'yes']);
        update_option('srbtranslatin_media', ['transliterate_uploads' => 'yes', 'separate_uploads' => 'yes']);
        update_option(Settings_Schema::OPTION_NAME, ['url_param' => 'script', 'transliterate_uploads' => '0', 'separate_uploads' => '0', 'injected' => 'bad']);
        $saved = get_option(Settings_Schema::OPTION_NAME);
        self::assertSame('lat', $saved['default_script']);
        self::assertSame('script', $saved['url_param']);
        self::assertFalse($saved['fix_search']);
        self::assertTrue($saved['fix_titles']);
        self::assertFalse($saved['transliterate_uploads']);
        self::assertTrue($saved['separate_uploads']);
        self::assertArrayNotHasKey('injected', $saved);
        self::assertSame(['default_script' => 'lat', 'url_param' => 'pismo'], get_option('srbtranslatin_general'));
    }

    public function test_invalid_submission_keeps_previously_saved_values(): void {
        update_option(Settings_Schema::OPTION_NAME, ['url_param' => 'pismo', 'fix_search' => '1']);
        update_option(Settings_Schema::OPTION_NAME, ['url_param' => ['bad'], 'fix_search' => 'invalid']);
        self::assertSame('pismo', get_option(Settings_Schema::OPTION_NAME)['url_param']);
        self::assertTrue(get_option(Settings_Schema::OPTION_NAME)['fix_search']);
        self::assertNotEmpty(get_settings_errors(Settings_Schema::OPTION_NAME));
    }

    public function test_form_contains_wordpress_nonce_and_escaped_values(): void {
        wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
        update_option(Settings_Schema::OPTION_NAME, ['url_param' => 'pismo', 'menu_title' => '" onfocus="alert(1)']);
        $_GET['tab'] = 'menu';
        ob_start();
        $this->page->render();
        $html = ob_get_clean();
        self::assertStringContainsString('action="options.php"', $html);
        self::assertMatchesRegularExpression('/name=[\"\']option_page[\"\'] value=[\"\']srbtranslatin-settings[\"\']/', $html);
        self::assertStringContainsString('name="_wpnonce"', $html);
        self::assertStringContainsString('&quot; onfocus=&quot;alert(1)', $html);
        self::assertStringNotContainsString('value="" onfocus=', $html);
        self::assertTrue(wp_verify_nonce(wp_create_nonce('srbtranslatin-settings-options'), 'srbtranslatin-settings-options') > 0);
    }

    public function test_options_group_uses_wordpress_admin_capability(): void {
        global $new_allowed_options;
        self::assertContains(Settings_Schema::OPTION_NAME, $new_allowed_options[Settings_Schema::PAGE_SLUG]);
        wp_set_current_user(self::factory()->user->create(['role' => 'subscriber']));
        self::assertFalse(current_user_can(apply_filters('option_page_capability_' . Settings_Schema::PAGE_SLUG, 'manage_options')));
        wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
        self::assertTrue(current_user_can(apply_filters('option_page_capability_' . Settings_Schema::PAGE_SLUG, 'manage_options')));
    }
}
