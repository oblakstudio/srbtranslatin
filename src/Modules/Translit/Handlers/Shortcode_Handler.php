<?php
/**
 * Shortcode_Handler class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Handlers;

use STL\Translit\Services\Shortcode_Service;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Handler;

/**
 * Register transliteration shortcodes.
 */
#[Handler( container: 'stl', strategy: Handler::INIT_JUST_IN_TIME )]
final class Shortcode_Handler {
    /**
     * Constructor.
     *
     * @param Shortcode_Service $service Shortcode runtime service.
     */
    public function __construct( private Shortcode_Service $service ) {
    }

    /**
     * Register shortcode callbacks on init.
     *
     * @return void
     */
    #[Action( tag: 'init', priority: 20, args: 0, invoke: Action::INV_PROXIED )]
    public function register_shortcodes(): void {
        $this->service->register_shortcodes();
    }
}
