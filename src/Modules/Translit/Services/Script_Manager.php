<?php
/**
 * Script_Manager class file
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Services;

use STL\Translit\Contracts\Persists_Script_Cookie;
use STL\Translit\Contracts\Resolves_Language;

/**
 * Resolve the effective transliteration script and language.
 */
final class Script_Manager {
    private const VALID_SCRIPTS       = array( 'cir', 'lat' );
    private const VALID_MODES         = array( 'lat', 'both' );
    private const SUPPORTED_LANGUAGES = array( 'sr_RS', 'mk_MK', 'bs_BA' );

    private string $script        = '';
    private string $language      = '';
    private string $script_source = '';

    public function __construct(
        private string $enabled_scripts,
        private string $default_script,
        private string $request_script,
        private string $cookie_script,
        private string $url_param,
        private ?Resolves_Language $language_resolver = null,
        private ?Persists_Script_Cookie $cookie_persister = null,
        private mixed $locale_getter = null,
    ) {
    }

    /**
     * Initialize the runtime script and language state.
     *
     * @return void
     */
    public function initialize(): void {
        [ $this->script, $this->script_source ] = $this->resolve_script();
        $this->language                         = $this->resolve_language();
    }

    /**
     * Get the chosen script.
     *
     * @return string
     */
    public function get_script(): string {
        return $this->script;
    }

    /**
     * Get the chosen language.
     *
     * @return string
     */
    public function get_language(): string {
        return $this->language;
    }

    /**
     * Get the source used to determine the chosen script.
     *
     * @return string
     */
    public function get_script_source(): string {
        return $this->script_source;
    }

    /**
     * Get the configured URL parameter used for script switching.
     *
     * @return string
     */
    public function get_url_param(): string {
        return $this->url_param;
    }

    /**
     * Check if the selected script is Latin.
     *
     * @return bool
     */
    public function is_latin(): bool {
        return 'lat' === $this->script;
    }

    /**
     * Check if transliteration should run.
     *
     * @return bool
     */
    public function should_transliterate(): bool {
        return $this->is_latin() && $this->is_supported_language();
    }

    /**
     * Check if the user can switch between scripts.
     *
     * @return bool
     */
    public function allows_selector(): bool {
        return 'both' === $this->normalize_mode();
    }

    /**
     * Resolve the current script and its source.
     *
     * @return array{0:string,1:string}
     */
    private function resolve_script(): array {
        if ( 'lat' === $this->normalize_mode() ) {
            return array( 'lat', 'mode' );
        }

        $requested_script = $this->normalize_script( $this->request_script );

        if ( '' !== $requested_script ) {
            $this->cookie_persister?->persist( $requested_script );

            return array( $requested_script, 'query' );
        }

        $cookie_script = $this->normalize_script( $this->cookie_script );

        if ( '' !== $cookie_script ) {
            return array( $cookie_script, 'cookie' );
        }

        return array( $this->normalize_default_script(), 'default' );
    }

    /**
     * Resolve the chosen language.
     *
     * @return string
     */
    private function resolve_language(): string {
        $language = $this->language_resolver?->resolve_language();

        if ( \is_string( $language ) && '' !== $language ) {
            return $language;
        }

        if ( \is_callable( $this->locale_getter ) ) {
            $locale = \call_user_func( $this->locale_getter );

            return \is_string( $locale ) && '' !== $locale ? $locale : \get_locale();
        }

        return \get_locale();
    }

    /**
     * Normalize the configured default script.
     *
     * @return string
     */
    private function normalize_default_script(): string {
        $default_script = $this->normalize_script( $this->default_script );

        return '' !== $default_script ? $default_script : 'cir';
    }

    /**
     * Normalize the enabled scripts mode.
     *
     * @return string
     */
    private function normalize_mode(): string {
        $mode = \sanitize_key( $this->enabled_scripts );

        return \in_array( $mode, self::VALID_MODES, true ) ? $mode : 'both';
    }

    /**
     * Normalize a script identifier to one of the supported values.
     *
     * @param string $script Raw script value.
     * @return string
     */
    private function normalize_script( string $script ): string {
        $script = \sanitize_key( $script );

        return \in_array( $script, self::VALID_SCRIPTS, true ) ? $script : '';
    }

    /**
     * Check if the resolved language supports Cyrillic-to-Latin transliteration.
     *
     * @return bool
     */
    private function is_supported_language(): bool {
        return \in_array( $this->language, self::SUPPORTED_LANGUAGES, true );
    }
}
