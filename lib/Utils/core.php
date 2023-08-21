<?php
/**
 * Utility functions.
 *
 * @package SrbTransLatin
 * @subpackage Utils
 */

use Oblak\STL\SrbTransLatin;

/**
 * SrbTransLatin instance.
 *
 * @return SrbTransLatin
 */
function STL() { // phpcs:ignore
    return SrbTransLatin::instance();
}

/**
 * Get settings array.
 *
 * @return array[][]
 */
function stl_get_settings_array() {
    return include STL_PLUGIN_PATH . 'config/settings.php';
}

/**
 * Get the available scripts for the website.
 *
 * @return string[]
 */
function stl_get_available_scripts() {
    return array(
        'cir' => __( 'Cyrillic', 'srbtranslatin' ),
        'lat' => __( 'Latin', 'srbtranslatin' ),
    );
}

/**
 * Get the current URL.
 *
 * @return string
 */
function stl_get_current_url() {
    global $wp;
	return home_url( add_query_arg( array(), $wp->request ) );
}
