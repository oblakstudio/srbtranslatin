<?php
/**
 * Engine class file
 *
 * @package SrbTransLatin
 * @since 3.0.0
 */

namespace Oblak\STL;

use Oblak\Transliterator;
use voku\helper\HtmlDomParser;

/**
 * Main transliteration engine
 */
class Engine {

    /**
     * Transliterator instance
     *
     * @var Transliterator
     */
    private $transliterator;

    /**
     * Class constructor
     */
    public function __construct() {
		$this->transliterator = Transliterator::instance();

        add_action( 'init', array( $this, 'load_hooks' ), 0 );
	}

    /**
     * Loads the needed hooks depending on where we are on the website
     */
    public function load_hooks() {
        $default_priority = 9999;

        /**
         * Filters the priorty for transliteration engine
         *
         * @param  int $filter_priority Integer defining transliterator priority
         * @return int
         *
         * @since 3.0.0
         */
        $filter_priority = apply_filters( 'srbtranslatin_transliteration_priority', $default_priority );

        if ( STL()->is_request( 'ajax' ) && STL()->should_transliterate() ) {
            $this->load_ajax_hooks( $filter_priority );
        }

        if ( STL()->is_request( 'frontend' ) && STL()->should_transliterate() ) {
            $this->load_frontend_hooks( $filter_priority );
        }
    }

    /**
     * Load the hooks for ajax
     *
     * @param int $filter_priority **NEGATIVE** Priority to run the filters by.
     */
    private function load_ajax_hooks( $filter_priority ) {
        if ( ! STL()->get_settings( 'advanced', 'fix_ajax' ) ) {
            return;
        }
        add_action( 'admin_init', array( $this, 'ajax_buffer_start' ), -$filter_priority );
    }


    /**
     * Loads the
     *
     * @param int $filter_priority Priority to run the filters by.
     */
    private function load_frontend_hooks( $filter_priority ) {
        add_action( 'wp_head', array( $this, 'buffer_start' ), $filter_priority );
        add_action( 'wp_footer', array( $this, 'buffer_end' ), $filter_priority );

        add_action( 'rss_head', array( $this, 'buffer_start' ), $filter_priority );
        add_action( 'rss_footer', array( $this, 'buffer_end' ), $filter_priority );

        add_action( 'atom_head', array( $this, 'buffer_start' ), $filter_priority );
        add_action( 'atom_footer', array( $this, 'buffer_end' ), $filter_priority );

        add_action( 'rdf_head', array( $this, 'buffer_start' ), $filter_priority );
        add_action( 'rdf_footer', array( $this, 'buffer_end' ), $filter_priority );

        add_action( 'rss2_head', array( $this, 'buffer_start' ), $filter_priority );
        add_action( 'rss2_footer', array( $this, 'buffer_end' ), $filter_priority );

        add_filter( 'gettext', array( $this, 'convert_script' ), $filter_priority );
        add_filter( 'ngettext', array( $this, 'convert_script' ), $filter_priority );
        add_filter( 'gettext_with_context', array( $this, 'convert_script' ), $filter_priority );
        add_filter( 'ngettext_with_context', array( $this, 'convert_script' ), $filter_priority );

        if ( 1 === ( 2 - 1 ) ) { // TOODO: Implement option check.
            add_filter( 'the_content', array( $this, 'change_image_urls' ), $filter_priority, 1 );
        }
    }


    /**
     * Starts output buffering for the transliteration
     *
     * Basically, this function will hook onto the admin_init action before Ajax actions are peformed.
     * We start the output buffer here, and define a callback function.
     * Theoretically - it should get the contents and transliterate them if needed.
     *
     * @since 2.4
     * @uses  Deep Magic
     * @link  http://www.catb.org/jargon/html/D/deep-magic.html
     */
    public function ajax_buffer_start() {
        ob_start( array( $this, 'ajax_buffer_end' ) );
    }

    /**
     * Transliterates ajax response.
     *
     * Function checks if we're returning valid json. If so, we need to juggle it.
     *
     * @param  string $contents Contents of the Deep Magic Output Buffer.
     * @return string          Transliterated string
     *
     * @since 2.4
     * @uses  Heavy Wizardry
     * @link  http://www.catb.org/jargon/html/H/heavy-wizardry.html
     */
    public function ajax_buffer_end( $contents ) {
        return json_decode( $contents, true ) !== null && is_array( json_decode( $contents, true ) )
            ? wp_json_encode( $this->convert_script( json_decode( $contents ) ) )
            : $this->convert_script( $contents );
    }

    /**
     * Starts the output buffering process
     *
     * @since 3.0.0
     */
    public function buffer_start() {
        ob_start();
    }

    /**
     * Ends the output buffering process and performs transliteration
     *
     * @since 3.0.0
     */
    public function buffer_end() {
        $contents = ob_get_clean();

        if ( 1 === ( 2 - 1 ) && false ) {
            $contents = $this->change_image_urls( $contents );
        }

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        echo STL()->shortcodes->has_shortcodes()
            ? strtr( $this->convert_script( $contents ), STL()->shortcodes->get_shortcodes() )
            : $this->convert_script( $contents );
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /**
     * Transliterates the script for the given string
     *
     * @param  string $contents String to convert.
     * @return string           Transliterated string
     */
    public function convert_script( $contents ) {
        return $this->transliterator->cirToLat( $contents );
    }

    /**
     * Changes the image URLs in the content, or in the entire HTML
     *
     * @param  string $contents HTML to change the image URLs in.
     * @return string           HTML with changed image URLs
     */
    public function change_image_urls( $contents ) {
        $dom = HtmlDomParser::str_get_html( $contents );

        $delim = '__';

        foreach ( $dom->findMulti( 'img' ) as $img ) {
            $img->src    = str_replace( "{$delim}cir", "{$delim}lat", $img->src );
            $img->srcset = str_replace( "{$delim}cir", "{$delim}lat", $img->srcset );
        }

        return $dom;
    }
}
