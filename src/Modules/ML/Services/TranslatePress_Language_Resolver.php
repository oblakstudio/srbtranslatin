<?php
/**
 * TranslatePress_Language_Resolver class file.
 *
 * @package SrbTransLatin
 * @subpackage ML
 */

namespace STL\ML\Services;

use STL\Translit\Contracts\Resolves_Language;

/**
 * Resolve the effective locale from TranslatePress.
 */
final class TranslatePress_Language_Resolver implements Resolves_Language {
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

        if ( isset( $GLOBALS['TRP_LANGUAGE'] ) && \is_string( $GLOBALS['TRP_LANGUAGE'] ) ) {
            return Language_Locale_Normalizer::normalize( $GLOBALS['TRP_LANGUAGE'] );
        }

        return null;
    }
}
