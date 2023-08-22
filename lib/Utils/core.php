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
 * Get suffixes for the available scripts.
 *
 * @param string $name_type Name type: native_name, english_name.
 */
function stl_get_script_suffixes( $name_type ) {
    $suffixes = array(
        'sr_RS'      => ' (Ћирилица)',
        'sr_latn_RS' => ' (Латиница)',
    );

    if ( 'english_name' === $name_type || ! str_contains( $name_type, 'sr' ) ) {
        $suffixes = array(
            'sr_RS'      => ' (Cyrillic)',
            'sr_latn_RS' => ' (Latin)',
        );
    }

    return $suffixes;
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

/**
 * Recursively remaps the keys of an array using the provided callback function.
 *
 * @param callable $callback The callback function to apply to each key.
 * @param array    $arr    The array to remap.
 *
 * @return array The remapped array.
 */
function stl_array_map_recursive( $callback, $arr ) {
    $result = array();
    foreach ( $arr as $key => $value ) {
        if ( is_array( $value ) ) {
            $result[ $key ] = stl_array_map_recursive( $callback, $value );
            continue;
        }

        $result[ $key ] = $callback( $value );
    }

    return $result;
}

/**
 * Display the script selector
 *
 * @param  array $args   Arguments.
 * @param  bool  $eecho  Whether to echo or return the output.
 * @return string|void   HTML for the script selector.
 */
function stl_script_selector( $args, $eecho = true ) {
	$args = wp_parse_args(
        $args,
        array(
			'selector_type' => 'online',
			'separator'     => '<span>&nbsp; | &nbsp;</span>',
			'cir_caption'   => 'Ћирилица',
			'lat_caption'   => 'Latinica',
			'inactive_only' => false,
			'active_script' => STL()->manager->get_script(),
			'cir_link'      => add_query_arg( STL()->manager->get_url_param(), 'cir', stl_get_current_url() ),
			'lat_link'      => add_query_arg( STL()->manager->get_url_param(), 'lat', stl_get_current_url() ),
        )
	);

    $scripts = array(
        array(
            'name'    => 'cir',
            'link'    => $args['cir_link'],
            'caption' => $args['cir_caption'],
        ),
        array(
            'name'    => 'lat',
            'link'    => $args['lat_link'],
            'caption' => $args['lat_caption'],
        ),
    );

    if ( 'dropdown' !== $args['selector_type'] && $args['inactive_only'] ) {
        $scripts = array_filter(
            $scripts,
            function ( $script ) use ( $args ) {
                return $script['name'] !== $args['active_script'];
            }
        );
    }

    $template = locate_template( '/templates/stl/selector-' . $args['selector_type'] . '.php' );

    if ( ! $template ) {
        $template = STL_PLUGIN_PATH . 'templates/selector-' . $args['selector_type'] . '.php';
    }

    if ( ! $eecho ) {
        ob_start();
    }

    echo '<div class="stl-script-selector">';

    include $template;

    echo '</div>';

    if ( ! $eecho ) {
        return ob_get_clean();
    }
}
