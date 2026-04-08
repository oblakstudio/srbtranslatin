<?php

namespace STL\Admin;

use STL\Admin\Services\Settings_Page_Registrar;
use STL\Admin\Settings\Admin_Context;
use STL\Admin\Settings\Definitions\Advanced_Tab_Definition;
use STL\Admin\Settings\Definitions\General_Tab_Definition;
use STL\Admin\Settings\Definitions\Media_Tab_Definition;
use STL\Admin\Settings\Definitions\Menu_Tab_Definition;
use STL\Admin\Settings\Settings_Page_Definition;
use STL\Admin\Settings\WordPress_Admin_Context;
use WPTechnix\WP_Settings_Builder\Settings_Builder;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Module;

/**
 * Admin module definition
 */
#[Module( container: 'stl', hook: 'srbtranslatin_loaded', priority: 20, handlers: array() )]
class Admin_Module {
    public static function can_initialize(): bool {
        return \is_admin();
    }

    /**
     * @return array<string,mixed>
     */
    public static function configure(): array {
        return array(
            Admin_Context::class            => \DI\autowire( WordPress_Admin_Context::class ),
            Advanced_Tab_Definition::class  => \DI\autowire(),
            General_Tab_Definition::class   => \DI\autowire(),
            Media_Tab_Definition::class     => \DI\autowire(),
            Menu_Tab_Definition::class      => \DI\autowire(),
            Settings_Builder::class         => \DI\factory(
                static fn(): Settings_Builder => new Settings_Builder(),
            ),
            Settings_Page_Definition::class => \DI\factory(
                static fn(
                    General_Tab_Definition $general,
                    Menu_Tab_Definition $menu,
                    Media_Tab_Definition $media,
                    Advanced_Tab_Definition $advanced,
                ): Settings_Page_Definition => new Settings_Page_Definition(
                    array( $general, $menu, $media, $advanced ),
                ),
            ),
            Settings_Page_Registrar::class  => \DI\autowire(),
        );
    }

    /**
     * Registers the settings page.
     *
     * @param Settings_Page_Registrar $registrar Settings page registrar instance. Injected by the container.
     */
    #[Action( tag: 'wp_loaded', priority: 100, invoke: Action::INV_PROXIED, args: 0 )]
    public function register_settings( Settings_Page_Registrar $registrar ): void {
        $registrar->register();
    }
}
