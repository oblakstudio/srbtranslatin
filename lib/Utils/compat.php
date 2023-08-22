<?php
/**
 * Compatibility functions for older versions of plugin
 *
 * @package SrbTransLatin
 * @subpackage Utils
 */

/**
 * Get the current script for the website
 *
 * @return string `cir` or `lat` Depending on the currently selected script
 * @since 1.0
 * @deprecated 3.0.0 `Use STL()->manager->get_script()` instead
 */
function stl_get_current_script() {
    _doing_it_wrong( __FUNCTION__, 'Use STL()->manager->get_script() instead', '3.0.0' );
    return STL()->manager->get_script();
}

/**
 * Check if the current script is Cyrillic
 *
 * @return bool `true` if the current script is Cyrillic, `false` otherwise
 * @since 1.0
 * @deprecated 3.0.0 `Use STL()->manager->is_cyrillic()` instead
 */
function stl_is_current_cyrillic() {
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
function stl_is_current_latin() {
    _doing_it_wrong( __FUNCTION__, 'Use STL()->manager->is_latin() instead', '3.0.0' );
    return STL()->manager->is_latin();
}

/**
 * Get the current script identifier
 *
 * @return string 'cir' - Always
 * @since 1.0
 * @deprecated 3.0.0 This is always 'cir'
 */
function stl_get_cyrillic_id() {
    _doing_it_wrong( __FUNCTION__, 'This is always "cir"', '3.0.0' );
    return 'cir';
}

/**
 * Get the current script identifier
 *
 * @return string 'lat' - Always
 * @since 1.0
 * @deprecated 3.0.0 This is always 'lat'
 */
function stl_get_latin_id() {
    _doing_it_wrong( __FUNCTION__, 'This is always "lat"', '3.0.0' );
    return 'lat';
}

/**
 * Get the current script identifier
 *
 * @return string Url parameter for script changing
 * @since 1.0
 * @deprecated 3.0.0 Use `STL()->manager->get_url_param()` instead
 */
function stl_get_script_identifier() {
    _doing_it_wrong( __FUNCTION__, 'Use STL()->manager->get_url_param()', '3.0.0' );
    return STL()->manager->get_url_param();
}
