<?php
/**
 * Media_Permalink_Service class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Services;

use Oblak\Transliterator;
use STL\Common\Settings\Plugin_Settings;

/**
 * Transliterate persisted media filenames and permalink titles.
 */
final class Media_Permalink_Service {
    /**
     * Constructor.
     *
     * @param Plugin_Settings     $settings Plugin settings storage.
     * @param Transliterator|null $transliterator Transliterator instance.
     */
    public function __construct(
        private Plugin_Settings $settings,
        private ?Transliterator $transliterator = null,
    ) {
        $this->transliterator ??= Transliterator::instance();
    }

    /**
     * Transliterate an uploaded filename to readable ASCII Latin.
     *
     * @param string $filename Sanitized filename.
     * @return string
     */
    public function transliterate_filename( string $filename ): string {
        if ( ! (bool) $this->settings->get( 'media', 'transliterate_uploads', true ) ) {
            return $filename;
        }

        return $this->transliterator->cirToCutLat( $filename );
    }

    /**
     * Transliterate a permalink title before WordPress generates the final slug.
     *
     * @param string $title Title being sanitized.
     * @param string $raw_title Raw title before sanitization.
     * @param string $context Sanitization context.
     * @return string
     */
    public function transliterate_permalink_slug( string $title, string $raw_title = '', string $context = 'display' ): string {
        unset( $raw_title );

        if ( 'save' !== $context || ! (bool) $this->settings->get( 'advanced', 'fix_permalinks', false ) ) {
            return $title;
        }

        return $this->transliterator->cirToCutLat( $title );
    }
}
