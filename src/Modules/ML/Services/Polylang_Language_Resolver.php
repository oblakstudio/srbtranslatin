<?php
/**
 * Polylang_Language_Resolver class file.
 *
 * @package SrbTransLatin
 * @subpackage ML
 */

namespace STL\ML\Services;

use STL\Translit\Contracts\Resolves_Language;

/**
 * Resolve the effective locale from Polylang.
 */
final class Polylang_Language_Resolver implements Resolves_Language {
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
        if ( \is_callable( $this->current_language_callback ) ) {
            $language = \call_user_func( $this->current_language_callback );

            return Language_Locale_Normalizer::normalize( \is_string( $language ) ? $language : null );
        }

        if ( \function_exists( 'pll_current_language' ) ) {
            $locale = \pll_current_language( 'locale' );

            if ( \is_string( $locale ) && '' !== $locale ) {
                return Language_Locale_Normalizer::normalize( $locale );
            }

            $language = \pll_current_language();

            return Language_Locale_Normalizer::normalize( \is_string( $language ) ? $language : null );
        }

        return null;
    }
}
