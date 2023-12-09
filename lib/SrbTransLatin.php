<?php
/**
 * SrbTransLatin class file.
 *
 * @package SrbTransLatin
 */

namespace Oblak\STL;

use Oblak\STL\Admin\Admin_Core;
use Oblak\WP\Settings_Helper_Trait;

/**
 * Main plugin class wrapping all of the functionalities
 */
class SrbTransLatin {
    use Settings_Helper_Trait;

    /**
     * Plugin instance
     *
     * @var SrbTransLatin
     */
    private static $instance = null;

    /**
     * Undocumented variable
     *
     * @var Core\Script_Manager
     */
    public $manager;

    /**
     * Shortcodes Manager
     *
     * @var Shortcode\Shortcode_Manager
     */
    public $shortcodes;

    /**
     * Transliteration engine
     *
     * @var Core\Engine
     */
    public $engine;

    /**
     * Multi-language plugin
     *
     * @var Core\Multi_Language
     */
    public $ml;

    /**
     * Get plugin instance
     *
     * @return SrbTransLatin
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Checks if the current request is of a certain type
     *
     * @param  string $type Request type: admin, ajax, cron, frontend.
     * @return bool         True if the current request is of the given type
     */
    public function is_request( $type ) {
        switch ( $type ) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined( 'DOING_AJAX' );
            case 'cron':
                return defined( 'DOING_CRON' );
            case 'frontend':
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

    /**
     * Determines if the website should be transliterated
     *
     * @return bool
     */
    public function should_transliterate() {
        return $this->manager->should_transliterate();
    }

    /**
     * Class constructor
     */
    private function __construct() {
        $this->settings = $this->load_settings( 'srbtranslatin', stl_get_settings_array()['settings'], null );
        $this->load_classes();
        $this->init_hooks();
    }



    /**
     * Loads the plugin classes
     * */
    private function load_classes() {
        $this->manager    = new Core\Script_Manager();
        $this->shortcodes = new Shortcode\Shortcode_Manager();
        $this->ml         = new Core\Multi_Language();

        if ( $this->is_request( 'admin' ) ) {
            new Admin_Core();
        }

        new Frontend\Search_Query_Transliterator();
    }

    /**
     * Plugin hooks
     */
    public function init_hooks() {
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
        add_action( 'plugins_loaded', array( $this, 'ml_plugin_compat' ), -1 );
        add_action( 'widgets_init', array( $this, 'register_widget' ) );
    }

    /**
     * Loads the plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'srbtranslatin', false, STL_PLUGIN_PATH . 'languages' );
    }

    /**
     * Actions to be performed when the plugin is loaded
     */
    public function on_plugins_loaded() {
        if ( $this->is_request( 'frontend' ) ) {
            new Frontend\Menu_Extender();
            new Frontend\Title_Transliterator();
        }

        $this->engine = new Core\Engine();

        /**
         * Fired when SrbTransLatin is loaded
         *
         * @since 3.0.0
         */
        do_action( 'srbtranslatin_loaded' );
    }

    /**
     * Loads the multi-language plugin compatibility
     */
    public function ml_plugin_compat() {
        switch ( $this->ml->get_ml_plugin() ) {
            case 'translatepress':
                // Nothing for now.
                break;
            case 'wpml':
                new Language\WPML();
                break;
        }
    }

    /**
     * Registers the widget
     */
    public function register_widget() {
        register_widget( Widget\Selector_Widget::class );
    }
}
