<?php // phpcs:disable Squiz.Commenting.FunctionComment.MissingParamTag
/**
 * Cyrilizator class file
 *
 * @package SrbTransLatin
 */

namespace Oblak\STL\Shortcode;

/**
 * Keeps content in Cyrillic even on latin pages
 */
class Cyrilizator extends Base_Shortcode {

    /**
     * Class constructor
     */
    public function __construct() {
        add_shortcode( 'stl_cyr', array( $this, 'do_shortcode' ) );
        add_shortcode( 'stl_cyrillic', array( $this, 'do_shortcode' ) );
    }

    /**
     * {@inheritDoc}
     */
    public function do_shortcode( $atts, $content = null, $tag = '' ) {
        if ( STL()->manager->is_cyrillic() || ! STL()->manager->is_serbian() ) {
            return $content;
        }

        return $this->encode_shortcode( $content );
    }
}
