<?php

namespace STL\Core;

use STL\Common\Settings\Plugin_Settings;
use STL\Common\Settings\WordPress_Option_Settings;
use WPTechnix\WP_Settings_Builder\Settings_Page;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Module;

#[Module( container: 'stl', hook: 'srbtranslatin_loaded', priority: 0 )]
class Core_Module {
    /**
     * Configure the module's dependencies and services.
     *
     * @return array<string,mixed>
     */
    public static function configure(): array {
        return array(
            'stl.settings'       => \DI\factory(
                static fn() => require STL_PATH . 'config/settings.php',
            ),
            Plugin_Settings::class => \DI\autowire( WordPress_Option_Settings::class ),
            Settings_Page::class => \DI\factory( Services\Settings_Page_Factory::make( ... ) )
                ->parameter( 'config', \DI\get( 'stl.settings' ) ),

            // Settings_Page_Config::class  => \DI\autowire( File_Settings_Page_Config::class ),
            // Settings_Page_Handler::class => \DI\autowire(),
        );
    }


    /**
     * Load the settings page.
     *
     * @param Settings_Page $page Settings page instance.
     * @return void
     */
    #[Action( tag: 'before_srbtranslatin_init', priority: 0, invoke: Action::INV_PROXIED, args: 0 )]
    public function load_settings( Settings_Page $page ): void {
        $page->init();
        $this->capture_test_settings_hooks();
    }

    /**
     * Mirror settings page hooks into the unit-test harness when present.
     *
     * @return void
     */
    private function capture_test_settings_hooks(): void {
        if ( ! \array_key_exists( 'stl_test_registered_actions', $GLOBALS ) || ! \is_array( $GLOBALS['stl_test_registered_actions'] ) ) {
            return;
        }

        foreach ( array( 'admin_menu', 'admin_init', 'admin_enqueue_scripts' ) as $hook ) {
            $GLOBALS['stl_test_registered_actions'][] = array(
                'hook' => $hook,
                'callback' => null,
                'priority' => 10,
                'accepted_args' => 1,
            );
        }
    }
}
