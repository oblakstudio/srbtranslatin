<?php
/**
 * Multi_Language_Settings_Page class file.
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
class Multi_Language_Settings_Page extends Plugin_Settings_Page {
    /**
     * Class constructor
     */
    public function __construct() {
        $this->slug  = 'srbtranslatin';
        $this->id    = STL()->ml->get_ml_plugin();
        $this->label = __( 'Multi_Language', 'srbtranslatin' );

        parent::__construct();
    }

    /**
     * Get settings for the default section
     */
    protected function get_settings_for_default_section() {
        // We need to determine the Multi_Language plugin we're using, and display only the needed settings.
        // This means we have to manually output the section start and end settings.

        return array_merge(
            array(
                array(
                    'title' => _x( 'Multi_Language settings', 'section name', 'srbtranslatin' ),
                    'type'  => 'title',
                    'desc'  => __( 'Settings for integrations with various Multi_Language plugins', 'srbtranslatin' ),
                    'id'    => 'srbtranslatin_Multi_Language_settings',
                ),
            ),
            stl_get_settings_array()['settings'][ $this->id ],
            array(
                array(
                    'type' => 'sectionend',
                    'id'   => 'srbtranslatin_Multi_Language_settings',
                ),
            ),
        );
    }
}
