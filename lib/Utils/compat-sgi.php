<?php
/**
 * Old compatibility functions
 *
 * @package SrbTransLatin
 */

namespace SGI\STL\Util;

/**
 * Get the current script for the website
 *
 * @return string `cir` or `lat` Depending on the currently selected script
 * @deprecated 3.0.0 `Use STL()->manager->get_script()` instead
 */
function get_script() {
    _doing_it_wrong( __FUNCTION__, 'Use STL()->manager->get-script()', '3.0.0' );
    return STL()->manager->get_script();
}

/**
 * Get the URL parameter for the script changing
 *
 * @return string
 * @deprecated 3.0.0 `Use STL()->manager->get_url_param()` instead
 */
function get_script_param() {
    _doing_it_wrong( __FUNCTION__, 'Use STL()->manager->get_url_param()', '3.0.0' );
    return STL()->manager->get_url_param();
}

/**
 * Check if the website is in Serbian language
 *
 * @deprecated 3.0.0 `Use STL()->manager->is_serbian()` instead
 * @return bool
 */
function is_serbian() {
    _doing_it_wrong( __FUNCTION__, 'Use STL()->manager->is_serbian()', '3.0.0' );
    return STL()->manager->is_serbian();
}

/**
 * Check if the WPML plugin is active
 *
 * @return bool
 * @deprecated 3.0.0 `Use STL()->ml->is_wpml_active()` instead
 */
function is_wpml_active() {
    _doing_it_wrong( __FUNCTION__, 'Use STL()->ml->is_wpml_active()', '3.0.0' );
    return STL()->ml->is_wpml_active();
}

/**
 * Check if the current script is Cyrillic
 *
 * @return bool `true` if the current script is Cyrillic, `false` otherwise
 * @since 1.0
 * @deprecated 3.0.0 `Use STL()->manager->is_cyrillic()` instead
 */
function is_cyrillic() {
    _doing_it_wrong( __FUNCTION__, 'Use STL()->manager->is_cyrillic() instead', '3.0.0' );
    return STL()->manager->is_cyrillic();
}

/**
 * Check if the current script is Latin
 *
 * @return bool `true` if the current script is Latin, `false` otherwise
 * @since 1.0
 * @deprecated 3.0.0 `Use STL()->manager->is_latin()` instead
 */
function is_latin() {
    _doing_it_wrong( __FUNCTION__, 'Use STL()->manager->is_latin() instead', '3.0.0' );
    return STL()->manager->is_latin();
}

/**
 * Get the current script identifier
 *
 * @return string Url parameter for script changing
 * @since 1.0
 * @deprecated 3.0.0 Use `STL()->manager->get_url_param()` instead
 */
function get_query_param() {
    _doing_it_wrong( __FUNCTION__, 'Use STL()->manager->get_url_param()', '3.0.0' );
    return STL()->manager->get_url_param();
}

/**
 * Main transliteration function which converts cyrillic script to latin
 *
 * @param  null|string $content String to transliterate.
 * @param  bool        $cut     Flag determining if transliteration is done to "cut" latin script.
 * @return null|string          Transliterated script
 */
function transliterate( ?string $content, bool $cut = false ) {
    _doing_it_wrong( __FUNCTION__, 'Use STL()->engine->convert_to_latin()', '3.0.0' );
    return STL()->engine->convert_to_latin( $content, $cut );
}

/**
 * Reverse transliteration function - Transliterates content from latin to cyrillic
 *
 * @param  string $content Content to perform reverse transliteration on.
 * @return string          Reverse-transliterated content
 *
 * @deprecated 3.0.0 Use `STL()->engine->
 */
function reverse_transliterate( $content ) {
    _doing_it_wrong( __FUNCTION__, 'Use STL()->engine->convert_to_cyrillic()', '3.0.0' );
    return STL()->engine->convert_to_cyrillic( $content );
}
