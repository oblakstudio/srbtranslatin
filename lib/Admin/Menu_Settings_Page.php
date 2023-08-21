<?php
/**
 * General_Settings_Page class file.
 *
 * @package srbtranslatin
 * @since 3.0.0
 */

namespace Oblak\STL\Admin;

use Oblak\WP\Plugin_Settings_Page;

/**
 * Displays general settings
 */
class Menu_Settings_Page extends Plugin_Settings_Page {

    /**
     * Class constructor
     */
    public function __construct() {
        $this->slug  = 'srbtranslatin';
        $this->id    = 'menu';
        $this->label = __( 'Menus', 'srbtranslatin' );

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function get_own_sections() {
        return array(
            '' => __( 'General', 'srbtranslatin' ),
        );
    }

    /**
     * Get settings for the default section
     */
    protected function get_settings_for_default_section() {
        return stl_get_settings_array()['settings']['menu'] ?? array();
    }
}
