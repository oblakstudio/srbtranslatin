<?php
/**
 * List script selector template
 *
 * @package SrbTransLatin
 * @subpackage Templates
 * @version 3.0.0
 */

if ( $args['inactive_only'] ) {
    $scripts = array_filter(
        $scripts,
        function( $script ) use ( $args ) {
			return $script['name'] !== $args['active_script'];
		}
    );
}

foreach ( $scripts as $index => $script ) {
    printf(
        '<a rel="nofollow" href="%s" style="%s">%s</a>',
        esc_url( $script['link'] ),
        ( $args['active_script'] === $script['name'] ) ? 'font-weight:700;' : '',
        esc_html( $script['caption'] )
    );

    echo ( 0 === $index ) && ! $args['inactive_only'] ? wp_kses_post( $args['separator'] ) : '';
}
