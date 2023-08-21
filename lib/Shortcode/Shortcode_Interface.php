<?php
/**
 * Shortcode_Interface file
 *
 * @package SrbTransLatin
 * @since 3.0.0
 */

namespace Oblak\STL\Shortcode;

/**
 * Interface common for all shortcodes used by the plugin
 */
interface Shortcode_Interface {
    /**
     * Shortcode callback which will be called when shortcode is used
     *
     * @param  array  $atts    Shortcode attributes.
     * @param  string $content Shortcode content.
     * @param  string $tag     Shortcode tag.
     * @return string          Shortcode output
     */
    public function do_shortcode( $atts, $content = null, $tag = '' );

    /**
     * Shortcode callback which will be called when shortcode is used
     *
     * Should return a filtered callback
     *
     * @param  string $contents Shortcode content.
     * @return string           Shortcode UUID, to be used for transliteration
     */
    public function encode_shortcode( $contents);
}
