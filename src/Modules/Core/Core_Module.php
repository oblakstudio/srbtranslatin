<?php

namespace STL\Core;

use WPTechnix\WP_Settings_Builder\Settings_Builder;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Module;

#[Module( container: 'stl', hook: 'srbtranslatin_loaded', priority: 0, imports: array(), handlers: array() )]
class Core_Module {
    /**
     * @return array<string,mixed>
     */
    public static function configure(): array {
        return array(
            Settings_Builder::class          => \DI\factory(
                static fn(): Settings_Builder => new Settings_Builder(),
            ),
            Settings_Page_Config::class      => \DI\autowire( File_Settings_Page_Config::class ),
            Settings_Page_Handler::class     => \DI\autowire(),
        );
    }

    #[Action( tag: 'init', priority: 0, invoke: Action::INV_PROXIED, args: 0 )]
    public function register_settings_page( Settings_Page_Handler $handler ): void {
        $handler->boot();
    }
}
