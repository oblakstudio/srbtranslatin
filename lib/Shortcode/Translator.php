<?php // phpcs:disable Squiz.Commenting.FunctionComment.MissingParamTag
/**
 * Translator class file
 *
 * @package SrbTransLatin
 */

namespace Oblak\STL\Shortcode;

/**
 * Enables custom transliteration for specific cyrillic words
 */
class Translator extends Base_Shortcode {
    /**
     * Class constructor
     */
    public function __construct() {
        add_shortcode( 'stl_translit', array( $this, 'do_shortcode' ) );
    }

    /**
     * {@inheritDoc}
     */
    public function do_shortcode( $atts, $content = null, $tag = '' ) {
        shortcode_atts( array( 'latin' => '' ), $atts, $tag );

        return STL()->manager->is_serbian() && STL()->manager->is_latin()
            ? $this->encode_shortcode( $atts['latin'] )
            : $content;
    }
}
