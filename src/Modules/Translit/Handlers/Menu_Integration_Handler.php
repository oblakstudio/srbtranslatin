<?php
/**
 * Menu_Integration_Handler class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Handlers;

use STL\Translit\Services\Menu_Integration_Service;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Handler;

/**
 * Register menu integration hooks.
 */
#[Handler( container: 'stl', strategy: Handler::INIT_JUST_IN_TIME )]
final class Menu_Integration_Handler {
    /**
     * Constructor.
     *
     * @param Menu_Integration_Service $service Menu integration service.
     * @param mixed                    $add_filter_callback Optional add_filter callback override.
     */
    public function __construct(
        private Menu_Integration_Service $service,
        private mixed $add_filter_callback = null,
    ) {
    }

    /**
     * Register selector block for block themes/site editor.
     *
     * @return void
     */
    #[Action( tag: 'init', priority: 5, args: 0, invoke: Action::INV_PROXIED )]
    public function register_block(): void {
        $this->service->register_selector_block();
    }

    /**
     * Register classic and block menu filters.
     *
     * @return void
     */
    #[Action( tag: 'srbtranslatin_init', priority: 20, args: 0, invoke: Action::INV_PROXIED )]
    public function register_hooks(): void {
        $add_filter = \is_callable( $this->add_filter_callback ) ? $this->add_filter_callback : '\add_filter';

        \call_user_func( $add_filter, 'wp_get_nav_menu_items', array( $this->service, 'inject_classic_menu_selector' ), 10, 2 );
        \call_user_func( $add_filter, 'render_block_core/navigation', array( $this->service, 'inject_navigation_block_selector' ), 10, 2 );
    }
}
