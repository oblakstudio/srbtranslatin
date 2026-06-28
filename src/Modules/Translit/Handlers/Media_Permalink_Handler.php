<?php
/**
 * Media_Permalink_Handler class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Handlers;

use STL\Translit\Services\Media_Permalink_Service;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Filter;
use XWP\DI\Decorators\Handler;

/**
 * Register media filename and permalink transliteration hooks.
 */
#[Handler( container: 'stl', strategy: Handler::INIT_JUST_IN_TIME )]
final class Media_Permalink_Handler {
    /**
     * Constructor.
     *
     * @param Media_Permalink_Service $service Media and permalink transliteration service.
     */
    public function __construct( private Media_Permalink_Service $service ) {
    }

    /**
     * Transliterate uploaded filenames.
     *
     * @param string $filename Sanitized filename.
     * @param string $raw_filename Raw uploaded filename.
     * @return string
     */
    #[Filter(
        tag: 'sanitize_file_name',
        priority: 999,
        context: Filter::CTX_GLOBAL,
        args: 2,
        invoke: Action::INV_PROXIED,
    )]
    public function sanitize_file_name( string $filename, string $raw_filename ): string {
        unset( $raw_filename );

        return $this->service->transliterate_filename( $filename );
    }

    /**
     * Transliterate permalink titles before WordPress generates the final slug.
     *
     * @param string $title Title being sanitized.
     * @param string $raw_title Raw title before sanitization.
     * @param string $context Sanitization context.
     * @return string
     */
    #[Filter(
        tag: 'sanitize_title',
        priority: 9,
        context: Filter::CTX_GLOBAL,
        args: 3,
        invoke: Action::INV_PROXIED,
    )]
    public function sanitize_title( string $title, string $raw_title, string $context ): string {
        return $this->service->transliterate_permalink_slug( $title, $raw_title, $context );
    }
}
