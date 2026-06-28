<?php
/**
 * Media_Handler class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Handlers;

use STL\Common\Settings\Plugin_Settings;
use STL\Translit\Services\Media_Service;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Filter;
use XWP\DI\Decorators\Handler;

/**
 * Register media transliteration hooks.
 */
#[Handler( container: 'stl', strategy: Handler::INIT_JUST_IN_TIME )]
final class Media_Handler {
    /**
     * Check whether content-only media URL rewriting should run.
     *
     * @param Plugin_Settings $settings Plugin settings.
     * @return bool
     */
    public static function should_rewrite_content( Plugin_Settings $settings ): bool {
        return 'content' === (string) $settings->get( 'media', 'transliteration_method', 'website' );
    }

    /**
     * Constructor.
     *
     * @param Media_Service $service Media service.
     */
    public function __construct( private Media_Service $service ) {
    }

    /**
     * Transliterate upload filenames.
     *
     * @param string $filename Upload filename.
     * @return string
     */
    #[Filter( tag: 'sanitize_file_name', priority: 999, args: 1, invoke: Action::INV_PROXIED )]
    public function transliterate_filename( string $filename ): string {
        return $this->service->transliterate_filename( $filename );
    }

    /**
     * Rewrite image URLs inside post content when content-only mode is selected.
     *
     * @param string $content Post content.
     * @return string
     */
    #[Filter(
        tag: 'the_content',
        priority: 9999,
        context: Filter::CTX_FRONTEND,
        conditional: array( self::class, 'should_rewrite_content' ),
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    public function rewrite_content_image_urls( string $content ): string {
        return $this->service->rewrite_image_urls( $content );
    }
}
