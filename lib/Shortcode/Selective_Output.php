<?php // phpcs:disable Squiz.Commenting.FunctionComment.MissingParamTag
/**
 * Selective_Output class file
 *
 * @package SrbTransLatin
 */

namespace Oblak\STL\Shortcode;

/**
 * Selective output shortcode.
 *
 * Outputs content only if the current script is the same as the one specified in the shortcode.
 *
 * @since 2.0.0
 */
class Selective_Output extends Base_Shortcode {
    /**
     * Class constructor
     */
    public function __construct() {
        add_shortcode( 'stl_selective_output', array( $this, 'do_shortcode' ) );
        add_shortcode( 'stl_show', array( $this, 'do_shortcode' ) );
    }

    /**
     * {@inheritDoc}
     */
    public function do_shortcode( $atts, $content = null, $tag = '' ) {
        shortcode_atts(
            array( 'script' => '' ),
            $atts,
            $tag
        );

        return STL()->manager->is_serbian() && str_starts_with( $atts['script'], STL()->manager->get_script() ) ? $content : '';
    }
}
