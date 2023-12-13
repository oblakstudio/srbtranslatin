<?php
/**
 * Multi_Language class file.
 *
 * @package SrbTransLatin
 */

namespace Oblak\STL\Core;

/**
 * Multi_Language class
 *
 * Compatibility with various Multi_Language plugins
 *
 * @since 3.0.0
 */
class Multi_Language {

    /**
     * Class constructor
     */
    public function __construct() {
    }

    /**
     * Determines if a Multi_Language plugin is active
     *
     * @return bool
     */
    public function ml_plugin_active() {
        return $this->is_translatepress_active() ||
            $this->is_polylang_active() ||
            $this->is_wpml_active();
    }

    /**
     * Determines which Multi_Language plugin is active
     *
     * @return string
     */
    public function get_ml_plugin() {
        if ( $this->is_translatepress_active() ) {
            return 'translatepress';
        } elseif ( $this->is_polylang_active() ) {
            return 'polylang';
        } elseif ( $this->is_wpml_active() ) {
            return 'wpml';
        } else {
            return 'none';
        }
    }

    /**
     * Determines the existence and functioning of TranslatePress plugin
     *
     * @return bool
     */
    public function is_translatepress_active() {
        return class_exists( 'TRP_Translate_Press' );
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
     * Checks if WPML plugin is active
     *
     * @return bool
     */
    public function is_wpml_active() {
        return class_exists( 'SitePress' );
    }

    /**
     * Get the website locale
     *
     * @return string
     */
    public function get_locale() {
        return $this->ml_plugin_active()
            ? $this->{"get_{$this->get_ml_plugin()}_locale"}()
            : $this->get_wp_locale();
    }

    /**
     * Get the current locale for the website
     *
     * @return string Current website locale
     */
    public function get_wp_locale() {
        return get_locale();
    }

    /**
     * Get the TranslatePress locale
     *
     * TranslatePress uses the WP locale
     *
     * @return string WP locale
     */
    public function get_translatepress_locale() {
        return $this->get_wp_locale();
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
     * Get the WPML locale
     *
     * @return string sr_RS for serbian locale, other for all others
     */
    public function get_wpml_locale() {
        // Documented in WPML.
        switch ( apply_filters( 'wpml_current_language', null ) ) {
            case 'sr':
                return 'sr_RS';
            case 'mk':
                return 'mk_MK';
            default:
                return 'other';
        }
    }
}
