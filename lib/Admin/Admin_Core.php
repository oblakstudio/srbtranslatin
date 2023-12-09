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
        add_action( 'sanitize_file_name', array( $this, 'convert_filename_to_latin' ), 999, 1 );
        add_action( 'sanitize_title', array( $this, 'convert_permalink_to_latin' ), 99, 1 );
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
        $pages[] = new Multi_Language_Settings_Page();
        $pages[] = new Advanced_Settings_Page();

        return $pages;
    }

    /**
     * Converts upload filename to latin
     *
     * @param string $filename Filename.
     * @return string          Converted filename.
     */
    public function convert_filename_to_latin( $filename ) {
        return STL()->get_settings( 'media', 'transliterate_uploads' )
            ? STL()->engine->convert_to_latin( $filename, true )
            : $filename;
    }

    /**
     * Converts permalink to latin
     *
     * @param string $title Permalink.
     * @return string       Converted permalink.
     */
    public function convert_permalink_to_latin( $title ) {
        return STL()->get_settings( 'advanced', 'fix_permalinks' )
            ? STL()->engine->convert_to_latin( $title )
            : $title;
    }
}
