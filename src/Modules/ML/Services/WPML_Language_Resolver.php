<?php
/**
 * WPML_Language_Resolver class file.
 *
 * @package SrbTransLatin
 * @subpackage ML
 */

namespace STL\ML\Services;

use STL\Translit\Contracts\Resolves_Language;

/**
 * Resolve the effective locale from WPML language codes.
 */
final class WPML_Language_Resolver implements Resolves_Language {
    /**
     * Constructor.
     *
     * @param mixed $current_language_callback Optional current-language callback for tests.
     */
    public function __construct( private mixed $current_language_callback = null ) {
    }

    /**
     * Resolve the active locale.
     *
     * @return string|null
     */
    public function resolve_language(): ?string {
        $language = $this->get_current_language();

        return match ( $language ) {
            'sr' => 'sr_RS',
            'mk' => 'mk_MK',
            default => null,
        };
    }

    /**
     * Get the current WPML language code.
     *
     * @return string|null
     */
    private function get_current_language(): ?string {
        if ( \is_callable( $this->current_language_callback ) ) {
            $language = \call_user_func( $this->current_language_callback );
            return \is_string( $language ) ? $language : null;
        }

        $language = \apply_filters( 'wpml_current_language', null );

        return \is_string( $language ) ? $language : null;
    }
}
