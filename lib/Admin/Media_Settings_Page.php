<?php
/**
 * Media_Settings_Page class file.
 *
 * @package SrbTransLatin
 */

namespace Oblak\STL\Admin;

use Oblak\WP\Plugin_Settings_Page;

/**
 * Displays media settings
 *
 * @since 3.0.0
 */
class Media_Settings_Page extends Plugin_Settings_Page {

    /**
     * Class constructor
     */
    public function __construct() {
        $this->slug  = 'srbtranslatin';
        $this->id    = 'media';
        $this->label = __( 'Media', 'srbtranslatin' );

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
        return stl_get_settings_array()['settings']['media'] ?? array();
    }
}
