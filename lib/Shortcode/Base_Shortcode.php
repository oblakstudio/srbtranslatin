<?php // phpcs:disable Squiz.Commenting.FunctionComment.MissingParamTag
/**
 * Base_Shortcode class file
 *
 * @package SrbTransLatin
 * @since 3.0.0
 */
namespace Oblak\STL\Shortcode;

/**
 * Base class for all shortcodes
 */
abstract class Base_Shortcode implements Shortcode_Interface {
    /**
     * {@inheritDoc}
     */
    public function encode_shortcode( $contents ) {
        /**
         * Adds the shortcode content to the list of encoded shortcodes
         *
         * @param string $contents Shortcode content
         * @return string          Shortcode UUID
         *
         * @since 3.0.0
         */
        return apply_filters( 'encode_shortcode', $contents );
    }

    /**
     * {@inheritDoc}
     */
    abstract public function do_shortcode( $atts, $content = null, $tag = '' );
}
