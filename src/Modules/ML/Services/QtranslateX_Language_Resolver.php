<?php
/**
 * QtranslateX_Language_Resolver class file.
 *
 * @package SrbTransLatin
 * @subpackage ML
 */

namespace STL\ML\Services;

use STL\Translit\Contracts\Resolves_Language;

/**
 * Resolve the effective locale from qTranslateX.
 */
final class QtranslateX_Language_Resolver implements Resolves_Language {
    /**
     * Constructor.
     *
     * @param mixed $current_language_callback Optional current-language callback for tests.
     * @param mixed $config_callback Optional qTranslateX config callback for tests.
     */
    public function __construct(
        private mixed $current_language_callback = null,
        private mixed $config_callback = null,
    ) {
    }

    /**
     * Resolve the active locale.
     *
     * @return string|null
     */
    public function resolve_language(): ?string {
        $language = $this->get_current_language();

        if ( null === $language ) {
            return null;
        }

        $config = $this->get_config();

        if ( isset( $config['locale'][ $language ] ) && \is_string( $config['locale'][ $language ] ) ) {
            return Language_Locale_Normalizer::normalize( $config['locale'][ $language ] );
        }

        return Language_Locale_Normalizer::normalize( $language );
    }

    /**
     * Get the active qTranslateX language.
     *
     * @return string|null
     */
    private function get_current_language(): ?string {
        if ( \is_callable( $this->current_language_callback ) ) {
            $language = \call_user_func( $this->current_language_callback );

            return \is_string( $language ) && '' !== $language ? $language : null;
        }

        if ( \function_exists( 'qtranxf_getLanguage' ) ) {
            $language = \qtranxf_getLanguage();

            return \is_string( $language ) && '' !== $language ? $language : null;
        }

        if ( isset( $GLOBALS['q_config']['language'] ) && \is_string( $GLOBALS['q_config']['language'] ) ) {
            return $GLOBALS['q_config']['language'];
        }

        return null;
    }

    /**
     * Get qTranslateX configuration.
     *
     * @return array<string,mixed>
     */
    private function get_config(): array {
        if ( \is_callable( $this->config_callback ) ) {
            $config = \call_user_func( $this->config_callback );

            return \is_array( $config ) ? $config : array();
        }

        return isset( $GLOBALS['q_config'] ) && \is_array( $GLOBALS['q_config'] ) ? $GLOBALS['q_config'] : array();
    }
}
