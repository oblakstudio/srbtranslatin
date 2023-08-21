<?php
/**
 * Script_Manager class file.
 *
 * @package SrbTransLatin
 * @since 3.0.0
 */

namespace Oblak\STL;

/**
 * Detects scripts and
 */
class Script_Manager {

    /**
     * Current website script
     *
     * @var string
     */
    private $script;

    /**
     * Website locale
     *
     * @var string
     */
    private $locale;

    /**
     * Class constructor
     */
    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'determine_script' ), 100 );
    }

    /**
     * Get the current locale for the website
     *
     * @return string Current website locale
     */
    public function get_locale() {
        $locale = 'sr_RS';

        if ( $this->is_polylang_active() ) {
            $locale = $this->get_polylang_locale();
        }

        if ( $this->is_wpml_active() ) {
            $locale = $this->get_wpml_locale();
        }

        return $locale;
    }

    /**
     * Checks if the current script is latin
     *
     * @return bool
     */
    public function is_latin() {
        return 'lat' === $this->get_script();
    }

    /**
     * Checks if the current script is cyrillic
     *
     * @return bool
     */
    public function is_cyrillic() {
        return 'cir' === $this->get_script();
    }

    /**
     * Checks if the current script is Serbian
     *
     * @return bool
     */
    public function is_serbian() {
        return 'sr_RS' === $this->get_locale();
    }

    /**
     * Determines the current script for the website
     */
    public function determine_script() {
        $requested_script = sanitize_text_field( wp_unslash( $_REQUEST[STL()->get_settings('general', 'url_param')] ?? '' ) ); //phpcs:ignore

        if ( '' !== $requested_script ) {
            $this->script = $this->set_cookie( $requested_script );
        }

        $this->script = $this->get_cookie();
        $this->locale = $this->get_locale();
    }

    /**
     * Get the current script if the site is in Serbian language
     *
     * @return string `cir` or `lat` Depending on the currently selected script
     */
    public function get_script() {
        return $this->script;
    }

    /**
     * Checks if the site should be transliterated
     *
     * @return bool True if the site should be transliterated, false otherwise
     */
    public function should_transliterate() {
        return 'sr_RS' === $this->locale && 'lat' === $this->script;
    }

    /**
     * Determines the existence and functioning of PolyLang plugin
     *
     * @return bool
     */
    public function is_polylang_active() {
        return function_exists( 'pll_the_languages' );
    }

    /**
     * Get the Polylang locale
     *
     * @return string Polylang locale
     */
    public function get_polylang_locale() {
        return pll_current_language( 'locale' );
    }

    /**
     * Checks if WPML plugin is active
     *
     * @return bool
     */
    public function is_wpml_active() {
        return defined( 'ICL_LANGUAGE_CODE' ) && class_exists( 'SitePress' );
    }

    /**
     * Get the WPML locale
     *
     * @return string sr_RS for serbian locale, other for all others
     */
    public function get_wpml_locale() {
        // Documented in WPML.
        return apply_filters( 'wpml_current_language', null ) === 'sr' ? 'sr_RS' : 'other';
    }

    /**
     * Get the cookie for the current script
     *
     * @return string Cookie value
     */
    private function get_cookie() {
        $cookie = sanitize_text_field( wp_unslash( $_COOKIE['stl_script'] ?? '' ) );
        return ! empty( $cookie ) ? $cookie : $this->set_cookie( STL()->get_settings( 'general', 'default_script' ) );
    }

    /**
     * Set the cookie for the current script
     *
     * @param  string $requested_script Requested script.
     * @return string                   Value that was set in the cookie
     */
    private function set_cookie( $requested_script ) {
        setcookie( 'stl_script', $requested_script, 0, '/', wp_parse_url( home_url() )['host'], is_ssl() );

        return $requested_script;
    }
}
