<?php
/**
 * Admin_Core class file.
 *
 * @package SrbTransLatin
 */

namespace Oblak\STL\Admin;

use Oblak\WP\Plugin_Settings_Manager;

/**
 * WP-Admin related functionality
 *
 * @since 3.0.0
 */
class Admin_Core {
    /**
     * Class constructor
     */
    public function __construct() {
        new Plugin_Settings_Manager( 'srbtranslatin', stl_get_settings_array() );

        add_action( 'srbtranslatin_get_settings_pages', array( $this, 'add_settings_pages' ) );
	}

    /**
     * Adds settings pages
     *
     * @param  array $pages Settings pages.
     * @return array        Modified settings pages.
     */
    public function add_settings_pages( $pages ) {
        $pages[] = new General_Settings_Page();
        $pages[] = new Media_Settings_Page();
        $pages[] = new Menu_Settings_Page();
        $pages[] = new Advanced_Settings_Page();

        return $pages;
    }
}
