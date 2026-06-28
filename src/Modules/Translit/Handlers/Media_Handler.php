<?php
/**
 * Media_Handler class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Handlers;

use STL\Translit\Services\Media_Service;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Filter;
use XWP\DI\Decorators\Handler;

/**
 * Register media filename and URL filters.
 */
#[Handler( container: 'stl', strategy: Handler::INIT_JUST_IN_TIME )]
final class Media_Handler {
    /**
     * Constructor.
     *
     * @param Media_Service $service Media service.
     */
    public function __construct( private Media_Service $service ) {
    }

    /**
     * Transliterate uploaded filenames.
     *
     * @param string $filename Sanitized filename.
     * @return string
     */
    #[Filter(
        tag: 'sanitize_file_name',
        priority: 999,
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    public function sanitize_file_name( string $filename ): string {
        return $this->service->sanitize_file_name( $filename );
    }

    /**
     * Rewrite media URLs in post content when content-only mode is active.
     *
     * @param string $content Post content.
     * @return string
     */
    #[Filter(
        tag: 'the_content',
        priority: 9999,
        context: Filter::CTX_FRONTEND,
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    public function rewrite_content_media_urls( string $content ): string {
        return $this->service->uses_content_method() ? $this->service->rewrite_content( $content ) : $content;
    }
}
