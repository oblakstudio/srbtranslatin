<?php
/**
 * WPML_Handler class file.
 *
 * @package SrbTransLatin
 * @subpackage ML
 */

namespace STL\ML\Handlers;

use STL\ML\Services\WPML_Service;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Filter;
use XWP\DI\Decorators\Handler;

/**
 * Register WPML compatibility hooks.
 */
#[Handler( container: 'stl', strategy: Handler::INIT_JUST_IN_TIME )]
final class WPML_Handler {
    /**
     * Constructor.
     *
     * @param WPML_Service $service WPML integration service.
     */
    public function __construct( private WPML_Service $service ) {
    }

    /**
     * Extend the WPML language selector.
     *
     * @param array<string,array<string,mixed>> $languages Language switcher data.
     * @return array<string,array<string,mixed>>
     */
    #[Filter(
        tag: 'icl_ls_languages',
        priority: 99,
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    public function extend_language_selector( array $languages ): array {
        return $this->service->extend_language_selector( $languages );
    }
}
