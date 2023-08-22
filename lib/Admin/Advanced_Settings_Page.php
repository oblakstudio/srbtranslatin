<?php
/**
 * Advanced_Settings_Page class file.
 *
 * @package SrbTransLatin
 */

namespace Oblak\STL\Admin;

use Oblak\WP\Plugin_Settings_Page;

/**
 * Advanced plugin settings
 *
 * @since 3.0.0
 */
class Advanced_Settings_Page extends Plugin_Settings_Page {
    /**
     * Class constructor
     */
    public function __construct() {
        $this->slug  = 'srbtranslatin';
        $this->id    = 'advanced';
        $this->label = __( 'Advanced', 'default' );

        parent::__construct();
    }

    /**
     * Get settings for the default section
     */
    protected function get_settings_for_default_section() {
        return stl_get_settings_array()['settings']['advanced'];
    }
}
