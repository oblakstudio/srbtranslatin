<?php
/**
 * Language_Locale_Normalizer class file.
 *
 * @package SrbTransLatin
 * @subpackage ML
 */

namespace STL\ML\Services;

/**
 * Normalize multilingual plugin language values to WordPress locales.
 */
final class Language_Locale_Normalizer {
    private const LANGUAGE_LOCALES = array(
        'sr' => 'sr_RS',
        'bs' => 'bs_BA',
        'mk' => 'mk_MK',
    );

    /**
     * Normalize a language code or locale.
     *
     * @param string|null $language Language code or locale.
     * @return string|null
     */
    public static function normalize( ?string $language ): ?string {
        if ( null === $language || '' === $language ) {
            return null;
        }

        if ( 1 === \preg_match( '/^[a-z]{2}_[A-Z]{2}$/', $language ) ) {
            return $language;
        }

        $language = \strtolower( \str_replace( '-', '_', $language ) );
        $language = \strtok( $language, '_' );

        return \is_string( $language ) && isset( self::LANGUAGE_LOCALES[ $language ] )
            ? self::LANGUAGE_LOCALES[ $language ]
            : null;
    }
}
