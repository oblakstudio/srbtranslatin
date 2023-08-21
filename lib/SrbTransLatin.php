<?php
/**
 * SrbTransLatin class file.
 *
 * @package SrbTransLatin
 */

namespace Oblak\STL;

use Oblak\STL\Admin\Admin_Core;
use Oblak\STL\Shortcode\Shortcode_Manager;
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
     * @var Script_Manager
     */
    public $manager;

    /**
     * Shortcodes Manager
     *
     * @var Shortcode_Manager
     */
    public $shortcodes;

    /**
     * Transliteration engine
     *
     * @var Engine
     */
    public $engine;

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
		$this->load_textdomain();
		$this->load_classes();
		$this->load_hooks();
	}

    /**
     * Loads the plugin textdomain
     */
    private function load_textdomain() {
        load_plugin_textdomain( 'srbtranslatin', false, STL_PLUGIN_PATH . 'languages' );
    }


    /**
     * Loads the plugin classes
     * */
	private function load_classes() {
		$this->manager    = new Script_Manager();
		$this->shortcodes = new Shortcode\Shortcode_Manager();
		$this->engine     = new Engine();

        if ( $this->is_request( 'admin' ) ) {
            new Admin_Core();
        }

        if ( $this->is_request( 'frontend' ) ) {
            new Frontend\Menu_Extender();
        }
    }

    /**
     * Loads the needed hooks depending on where we are on the website
     */
	private function load_hooks() {
	}
}
