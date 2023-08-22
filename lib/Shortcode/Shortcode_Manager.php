<?php
/**
 * Shortcode_Manager class file
 *
 * @package SrbTransLatin
 * @since 3.0.0
 */

namespace Oblak\STL\Shortcode;

/**
 * Manages the plugin shortcodes
 */
class Shortcode_Manager {
    /**
     * Shortcode array
     *
     * It is an array of `UUID` => string value pairs
     *
     * @var string[]
     */
    private $shortcodes = array();

    /**
     * Class constructor
     */
    public function __construct() {
        $this->load_shortcode_classes();
        add_filter( 'encode_shortcode', array( $this, 'get_shortcode_uuid' ) );
    }

    /**
     * Loads the shortcode classes
     */
    private function load_shortcode_classes() {
        $shortcode_classes = array(
            Cyrilizator::class,
            Selective_Output::class,
            Translator::class,
        );

        foreach ( $shortcode_classes as $shortcode_class ) {
            new $shortcode_class();
        }
    }

    /**
     * Returns the shortcode UUID
     *
     * @param  string $contents Shortcode content.
     * @return string           Shortcode UUID
     */
    public function get_shortcode_uuid( $contents ) {
        $uuid = wp_generate_uuid4();

        $this->shortcodes[ $uuid ] = $contents;

        return $uuid;
    }

    /**
     * Checks if we have any shortcodes
     *
     * @return bool
     */
    public function has_shortcodes() {
        return ! empty( $this->shortcodes );
    }

    /**
     * Returns the shortcodes
     *
     * @return string[]
     */
    public function get_shortcodes() {
        return $this->shortcodes;
    }
}
