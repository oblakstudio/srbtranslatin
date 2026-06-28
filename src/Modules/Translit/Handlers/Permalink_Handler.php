<?php
/**
 * Permalink_Handler class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Handlers;

use STL\Translit\Services\Permalink_Service;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Filter;
use XWP\DI\Decorators\Handler;

/**
 * Register permalink transliteration filters.
 */
#[Handler( container: 'stl', strategy: Handler::INIT_JUST_IN_TIME )]
final class Permalink_Handler {
    /**
     * Constructor.
     *
     * @param Permalink_Service $service Permalink service.
     */
    public function __construct( private Permalink_Service $service ) {
    }

    /**
     * Transliterate titles passed through WordPress slug sanitization.
     *
     * @param string $title Sanitized title.
     * @return string
     */
    #[Filter(
        tag: 'sanitize_title',
        priority: 99,
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    public function sanitize_title( string $title ): string {
        return $this->service->sanitize_title( $title );
    }
}
