<?php
/**
 * General_Settings_Page class file.
 *
 * @package SrbTransLatin
 */

namespace Oblak\STL\Admin;

use Oblak\WP\Plugin_Settings_Page;

/**
 * Displays general settings
 *
 * @since 3.0.0
 */
class General_Settings_Page extends Plugin_Settings_Page {

    /**
     * Class constructor
     */
    public function __construct() {
        $this->slug  = 'srbtranslatin';
        $this->id    = 'general';
        $this->label = __( 'General', 'srbtranslatin' );

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
        return stl_get_settings_array()['settings']['general'];
    }
}
