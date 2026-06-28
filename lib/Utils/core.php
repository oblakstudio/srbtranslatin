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
    return include STL_PATH . 'config/settings.php';
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
    if ( function_exists( 'xwp_has' ) && xwp_has( 'stl' ) ) {
        $container = xwp_app( 'stl' );

        if ( method_exists( $container, 'has' ) && $container->has( \STL\Translit\Services\Menu_Integration_Service::class ) ) {
            $markup = $container->get( \STL\Translit\Services\Menu_Integration_Service::class )->render_compat_selector( (array) $args );

            if ( $eecho ) {
                echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                return;
            }

            return $markup;
        }
    }

    $args = (array) $args;

    if ( ! isset( $args['active_script'], $args['cir_link'], $args['lat_link'] ) ) {
        $manager = STL()->manager;
        $url     = stl_get_current_url();

        $args += array(
            'active_script' => $manager->get_script(),
            'cir_link'      => add_query_arg( $manager->get_url_param(), 'cir', $url ),
            'lat_link'      => add_query_arg( $manager->get_url_param(), 'lat', $url ),
        );
    }

	$args = wp_parse_args(
        $args,
        array(
			'selector_type' => 'oneline',
			'separator'     => '<span>&nbsp; | &nbsp;</span>',
			'cir_caption'   => 'Ћирилица',
			'lat_caption'   => 'Latinica',
			'inactive_only' => false,
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
        $template = STL_PATH . 'templates/selector-' . $args['selector_type'] . '.php';
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

/**
 * Display the script selector.
 *
 * Legacy compatibility alias for stl_script_selector().
 *
 * @param array $args  Arguments.
 * @param bool  $eecho Whether to echo or return the output.
 * @return string|void HTML for the script selector.
 */
function stl_selector( $args = array(), $eecho = true ) {
    return stl_script_selector( $args, $eecho );
}
