<?php
    /**
     * Settings config
     *
     * @package srbtranslatin
     * @subpackage Config
     */

defined( 'ABSPATH' ) || exit;

$wplang = get_locale();

$disable_permalinks = 'sr_RS' === $wplang || 'bs_BA' === $wplang;
$navigation_menus   = get_registered_nav_menus();

$nav_menus = 0 === count( $navigation_menus ) ? false : true;

return array(
    'name'     => __( 'srbtranslatin - Serbian Latinisation', 'srbtranslatin' ),
    'basename' => STL_PLUGIN_BASENAME,
    'meta'     => array(
        array(
            'link' => 'https://oblak.studio/plugins/srbtranslatin/',
            'text' => __( 'Documentation', 'srbtranslatin' ),
        ),
    ),
    'page'     => array(
        'root'       => true,
        'parent'     => 'options-general.php',
        'title'      => __( 'Latinisation', 'srbtranslatin' ),
        'menu_title' => __( 'Settings', 'default' ),
        'cap'        => 'manage_options',
        'image'  => file_get_contents( STL_PLUGIN_PATH . 'assets/images/stl-logo.svg' ), // phpcs:ignore
        'prio'       => 99,
    ),
    'settings' => array(
        'general'        => array(
            array(
                'title' => _x( 'General settings', 'section name', 'srbtranslatin' ),
                'type'  => 'title',
                'desc'  => __( 'General settings control main functionality of the plugin', 'srbtranslatin' ),
                'id'    => 'srbtranslatin_general_settings',
            ),

            array(
                'title'   => __( 'Default script', 'srbtranslatin' ),
                'desc'    => __( 'Default script used for the website if user did not select a script', 'srbtranslatin' ),
                'id'      => 'default_script',
                'type'    => 'select',
                'default' => 'cir',
                'options' => stl_get_available_scripts(),
            ),

            array(
                'title'   => __( 'URL Parameter', 'srbtranslatin' ),
                'id'      => 'url_param',
                'desc'    => __( 'URL parameter used for script selector', 'srbtranslatin' ),
                'type'    => 'text',
                'default' => 'pismo',
            ),

            array(
                'type' => 'sectionend',
                'id'   => 'srbtranslatin_general_settings',
            ),

            array(
                'title' => _x( 'Menu settings', 'section name', 'srbtranslatin' ),
                'type'  => 'title',
                'desc'  => __( 'Options that control the display of the script selector', 'srbtranslatin' ),
                'id'    => 'srbtranslatin_menu_settings',
            ),

            array(
                'type' => 'sectionend',
                'id'   => 'srbtranslatin_menu_settings',
            ),
        ),
        'menu'           => array(
            array(
                'title' => _x( 'Navigation menu settings', 'section name', 'srbtranslatin' ),
                'type'  => 'title',
                'desc'  => __( 'Menu settings control extending and tweaking the script selector in theme menus', 'srbtranslatin' ),
                'id'    => 'srbtranslatin_menu_settings',
            ),
            array(
                'id'   => 'srbtranslatin_menu_warning',
                'type' => 'info',
                'text' => ! $nav_menus
                    ? '<strong>' . __( 'Options in this section are disabled because you do not have any navigation menus registered', 'srbtranslatin' ) . '</strong>'
                    : '',
            ),

            array(
                'title'    => __( 'Extend navigation menu', 'srbtranslatin' ),
                'desc'     => __( 'Adds a script selector to the navigation menu', 'srbtranslatin' ),
                'id'       => 'extend',
                'type'     => 'checkbox',
                'disabled' => ! $nav_menus,
                'default'  => 'yes',
            ),

            array(
                'title'    => __( 'Navigation menu to extend', 'srbtranslatin' ),
                'desc'     => __( 'Select the navigation menu you want to extend', 'srbtranslatin' ),
                'id'       => 'extend_menu',
                'type'     => 'select',
                'options'  => array_merge(
                    array( '' => __( 'Select a menu', 'srbtranslatin' ) ),
                    $navigation_menus,
                ),
                'disabled' => ! $nav_menus,
                'default'  => '',
            ),

            array(
                'title'    => __( 'Selector type', 'srbtranslatin' ),
                'desc'     => __( 'Choose the type of the script selector', 'srbtranslatin' ),
                'id'       => 'selector_type',
                'type'     => 'select',
                'options'  => array(
                    'submenu' => __( 'Submenu', 'srbtranslatin' ),
                    'inline'  => __( 'Inline', 'srbtranslatin' ),
                ),
                'default'  => 'submenu',
                'disabled' => ! $nav_menus,

            ),

            array(
                'title'    => __( 'Menu item title', 'srbtranslatin' ),
                'desc'     => __( 'Title of the menu item', 'srbtranslatin' ),
                'id'       => 'menu_title',
                'type'     => 'text',
                'default'  => __( 'Script', 'srbtranslatin' ),
                'disabled' => ! $nav_menus,

            ),

            array(
                'type' => 'sectionend',
                'id'   => 'srbtranslatin_media_settings',
            ),
        ),
        'media'          => array(
            array(
                'title' => _x( 'File and Media settings', 'section name', 'srbtranslatin' ),
                'type'  => 'title',
                'desc'  => __( 'File and media settings control filename transliteration and media saving', 'srbtranslatin' ),
                'id'    => 'srbtranslatin_media_settings',
            ),

            array(
                'title'   => __( 'Transliterate uploads', 'srbtranslatin' ),
                'type'    => 'checkbox',
                'desc'    => __( 'Transliterate filenames on upload', 'srbtranslatin' ),
                'default' => 'yes',
                'id'      => 'transliterate_uploads',
            ),

            array(
                'title'   => __( 'Script specific filenames', 'srbtranslatin' ),
                'type'    => 'checkbox',
                'desc'    => __( 'Check this box if you want to have separate filenames for each script', 'srbtranslatin' ),
                'id'      => 'separate_uploads',
                'default' => 'yes',
            ),

            array(
                'title'   => __( 'Filename separator', 'srbtranslatin' ),
                'type'    => 'text',
                'desc'    => __( 'Separator used for script specific filenames', 'srbtranslatin' ),
                'id'      => 'filename_separator',
                'class'   => 'small-text',
                'default' => '-',
            ),

            array(
                'title'   => __( 'Transliteration method', 'srbtranslatin' ),
                'type'    => 'select',
                'desc'    => __( 'Choose if you want to limit the script specific filenames on the entire website, or in content only', 'srbtranslatin' ),
                'id'      => 'transliteration_method',
                'options' => array(
                    'website' => __( 'Entire website', 'srbtranslatin' ),
                    'content' => __( 'Content only', 'srbtranslatin' ),
                ),
                'default' => 'website',
            ),

            array(
                'type' => 'sectionend',
                'id'   => 'srbtranslatin_media_settings',
            ),
        ),
        'wpml'           => array(
            array(
                'id'      => 'extend_ls',
                'title'   => __( 'Enable', 'srbtranslatin' ),
                'type'    => 'checkbox',
                'default' => 'yes',
                'desc'    => __( 'Extend WPML Language Switcher', 'srbtranslatin' ),
            ),
        ),
        'polylang'       => array(),
        'translatepress' => array(),
        'advanced'       => array(
            array(
                'title' => _x( 'Advanced settings', 'section name', 'srbtranslatin' ),
                'type'  => 'title',
                'desc'  => __( 'Advanced settings control permalink and search settings', 'srbtranslatin' ),
                'id'    => 'srbtranslatin_advanced_settings',
            ),

            array(
                'title'    => __( 'Fix Permalinks', 'srbtranslatin' ),
                'desc'     => __( 'Fixes permalinks for cyrillic scripts', 'srbtranslatin' ),
                'id'       => 'fix_permalinks',
                'type'     => 'checkbox',
                'default'  => 'no',
                'disabled' => $disable_permalinks,
                'tooltip'  => $disable_permalinks
                    ? sprintf(
                        // translators: %s is the current locale.
                        __( 'This option is currently disabled because your current locale is set to %s which will automatically change permalnks', 'srbtranslatin' ),
                        $wplang
                    )
                    : null,
            ),

            array(
                'title'   => __( 'Fix Search', 'srbtranslatin' ),
                'desc'    => __( 'Enables searching cyrillic content via latin script', 'srbtranslatin' ),
                'id'      => 'fix_search',
                'type'    => 'checkbox',
                'default' => 'no',
            ),

            array(
                'title'   => __( 'Fix Ajax', 'srbtranslatin' ),
                'desc'    => __( 'Transliterates ajax calls', 'srbtranslatin' ),
                'id'      => 'fix_ajax',
                'type'    => 'checkbox',
                'default' => 'no',
            ),

            array(
                'title'   => __( 'Fix Titles', 'srbtranslatin' ),
                'desc'    => __( 'Fixes titles for cyrillic scripts', 'srbtranslatin' ),
                'id'      => 'fix_titles',
                'type'    => 'checkbox',
                'default' => 'no',
            ),

            array(
                'type' => 'sectionend',
                'id'   => 'srbtranslatin_advanced_settings',
            ),
        ),
    ),
);
