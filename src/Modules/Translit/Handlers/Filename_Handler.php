<?php
/**
 * Preserve filename and permalink transliteration from the 3.x runtime.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Translit\Handlers;

use Oblak\Transliterator;
use STL\Common\Settings\Plugin_Settings;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Filter;
use XWP\DI\Decorators\Handler;

#[Handler( container: 'stl', strategy: Handler::INIT_JUST_IN_TIME )]
final class Filename_Handler {
    public function __construct( private Plugin_Settings $settings ) {
    }

    #[Filter( tag: 'sanitize_file_name', priority: 999, args: 1, invoke: Action::INV_PROXIED )]
    public function transliterate_filename( string $filename ): string {
        return $this->settings->get( 'media', 'transliterate_uploads', true )
            ? Transliterator::cirToCutLat( $filename )
            : $filename;
    }

    // Convert before core's priority-10 slug sanitization percent-encodes Cyrillic.
    #[Filter( tag: 'sanitize_title', priority: 9, args: 3, invoke: Action::INV_PROXIED )]
    public function transliterate_permalink( string $title, string $raw_title = '', string $context = 'save' ): string {
        return 'save' === $context && $this->settings->get( 'advanced', 'fix_permalinks', false )
            ? \remove_accents( Transliterator::cirToLat( $title ) )
            : $title;
    }
}
