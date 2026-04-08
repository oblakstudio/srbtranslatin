<?php
/**
 * Settings page schema.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$locale = \get_locale();
$disable_permalinks = \in_array( $locale, array( 'sr_RS', 'bs_BA' ), true );
$navigation_menus = \get_registered_nav_menus();
$has_navigation_menus = 0 < \count( $navigation_menus );

return array(
    'page' => array(
        'option_name' => \STL\Common\Settings\Settings_Schema::OPTION_NAME,
        'slug'        => \STL\Common\Settings\Settings_Schema::PAGE_SLUG,
        'page_title'  => \__( 'Latinisation', 'srbtranslatin' ),
        'menu_title'  => \__( 'Settings', 'default' ),
        'parent_slug' => 'options-general.php',
        'capability'  => 'manage_options',
    ),
    'tabs' => array(
        array(
            'id'    => 'general',
            'title' => \__( 'General', 'srbtranslatin' ),
            'icon'  => 'dashicons-admin-generic',
        ),
        array(
            'id'    => 'menu',
            'title' => \__( 'Menus', 'srbtranslatin' ),
            'icon'  => 'dashicons-menu',
        ),
        array(
            'id'    => 'media',
            'title' => \__( 'Media', 'srbtranslatin' ),
            'icon'  => 'dashicons-format-image',
        ),
        array(
            'id'    => 'advanced',
            'title' => \__( 'Advanced', 'default' ),
            'icon'  => 'dashicons-admin-tools',
        ),
    ),
    'sections' => array(
        array(
            'id'          => 'general',
            'title'       => \_x( 'General settings', 'section name', 'srbtranslatin' ),
            'description' => \__( 'General settings control main functionality of the plugin', 'srbtranslatin' ),
            'tab'         => 'general',
        ),
        array(
            'id'          => 'menu',
            'title'       => \_x( 'Navigation menu settings', 'section name', 'srbtranslatin' ),
            'description' => \__( 'Menu settings control extending and tweaking the script selector in theme menus', 'srbtranslatin' ),
            'tab'         => 'menu',
        ),
        array(
            'id'          => 'media',
            'title'       => \_x( 'File and Media settings', 'section name', 'srbtranslatin' ),
            'description' => \__( 'File and media settings control filename transliteration and media saving', 'srbtranslatin' ),
            'tab'         => 'media',
        ),
        array(
            'id'          => 'advanced',
            'title'       => \_x( 'Advanced settings', 'section name', 'srbtranslatin' ),
            'description' => \__( 'Advanced settings control permalink and search settings', 'srbtranslatin' ),
            'tab'         => 'advanced',
        ),
    ),
    'fields' => array(
        array(
            'id'      => 'enabled_scripts',
            'type'    => 'buttons_group',
            'title'   => \__( 'Enabled scripts', 'srbtranslatin' ),
            'section' => 'general',
            'extras'  => array(
                'default'     => 'both',
                'description' => \__( 'Cyrillic and Latin', 'srbtranslatin' ),
                'options'     => array(
                    'cir'  => 'Ћ ' . \__( 'Cyrillic', 'srbtranslatin' ),
                    'lat'  => 'Ć ' . \__( 'Latin', 'srbtranslatin' ),
                    'both' => 'Ć Ћ ' . \__( 'Both', 'srbtranslatin' ),
                ),
            ),
        ),
        array(
            'id'      => 'default_script',
            'type'    => 'select',
            'title'   => \__( 'Default script', 'srbtranslatin' ),
            'section' => 'general',
            'extras'  => array(
                'default'     => 'cir',
                'description' => \__( 'Default script used for the website if user did not select a script', 'srbtranslatin' ),
                'options'     => array(
                    'cir' => \__( 'Cyrillic', 'srbtranslatin' ),
                    'lat' => \__( 'Latin', 'srbtranslatin' ),
                ),
                'conditions'  => array(
                    'rules' => array(
                        array(
                            'field'    => 'enabled_scripts',
                            'operator' => '=',
                            'value'    => 'both',
                        ),
                    ),
                ),
            ),
        ),
        array(
            'id'      => 'url_param',
            'type'    => 'text',
            'title'   => \__( 'URL Parameter', 'srbtranslatin' ),
            'section' => 'general',
            'extras'  => array(
                'default'     => 'pismo',
                'description' => \__( 'URL parameter used for script selector', 'srbtranslatin' ),
            ),
        ),
        array(
            'id'      => 'menu_warning',
            'type'    => 'description',
            'title'   => '',
            'section' => 'menu',
            'extras'  => array(
                'description'     => ! $has_navigation_menus
                    ? '<strong>' . \__( 'Options in this section are disabled because you do not have any navigation menus registered', 'srbtranslatin' ) . '</strong>'
                    : '',
                'html_attributes' => array(
                    'class' => 'notice inline notice-warning',
                ),
            ),
        ),
        array(
            'id'      => 'extend',
            'type'    => 'checkbox',
            'title'   => \__( 'Extend navigation menu', 'srbtranslatin' ),
            'section' => 'menu',
            'extras'  => array(
                'default'         => true,
                'description'     => \__( 'Adds a script selector to the navigation menu', 'srbtranslatin' ),
                'html_attributes' => array(
                    'disabled' => ! $has_navigation_menus,
                ),
            ),
        ),
        array(
            'id'      => 'extend_menu',
            'type'    => 'select',
            'title'   => \__( 'Navigation menu to extend', 'srbtranslatin' ),
            'section' => 'menu',
            'extras'  => array(
                'default'         => '',
                'description'     => \__( 'Select the navigation menu you want to extend', 'srbtranslatin' ),
                'options'         => \array_merge(
                    array( '' => \__( 'Select a menu', 'srbtranslatin' ) ),
                    $navigation_menus,
                ),
                'html_attributes' => array(
                    'disabled' => ! $has_navigation_menus,
                ),
            ),
        ),
        array(
            'id'      => 'selector_type',
            'type'    => 'select',
            'title'   => \__( 'Selector type', 'srbtranslatin' ),
            'section' => 'menu',
            'extras'  => array(
                'default'         => 'submenu',
                'description'     => \__( 'Choose the type of the script selector', 'srbtranslatin' ),
                'options'         => array(
                    'submenu' => \__( 'Submenu', 'srbtranslatin' ),
                    'inline'  => \__( 'Inline', 'srbtranslatin' ),
                ),
                'html_attributes' => array(
                    'disabled' => ! $has_navigation_menus,
                ),
            ),
        ),
        array(
            'id'      => 'menu_title',
            'type'    => 'text',
            'title'   => \__( 'Menu item title', 'srbtranslatin' ),
            'section' => 'menu',
            'extras'  => array(
                'default'         => \__( 'Script', 'srbtranslatin' ),
                'description'     => \__( 'Title of the menu item', 'srbtranslatin' ),
                'html_attributes' => array(
                    'disabled' => ! $has_navigation_menus,
                ),
            ),
        ),
        array(
            'id'      => 'transliterate_uploads',
            'type'    => 'checkbox',
            'title'   => \__( 'Transliterate uploads', 'srbtranslatin' ),
            'section' => 'media',
            'extras'  => array(
                'default'     => true,
                'description' => \__( 'Transliterate filenames on upload', 'srbtranslatin' ),
            ),
        ),
        array(
            'id'      => 'separate_uploads',
            'type'    => 'checkbox',
            'title'   => \__( 'Script specific filenames', 'srbtranslatin' ),
            'section' => 'media',
            'extras'  => array(
                'default'     => true,
                'description' => \__( 'Check this box if you want to have separate filenames for each script', 'srbtranslatin' ),
            ),
        ),
        array(
            'id'      => 'filename_separator',
            'type'    => 'text',
            'title'   => \__( 'Filename separator', 'srbtranslatin' ),
            'section' => 'media',
            'extras'  => array(
                'default'         => '-',
                'description'     => \__( 'Separator used for script specific filenames', 'srbtranslatin' ),
                'html_attributes' => array(
                    'class' => 'small-text',
                ),
            ),
        ),
        array(
            'id'      => 'transliteration_method',
            'type'    => 'select',
            'title'   => \__( 'Transliteration method', 'srbtranslatin' ),
            'section' => 'media',
            'extras'  => array(
                'default'     => 'website',
                'description' => \__( 'Choose if you want to limit the script specific filenames on the entire website, or in content only', 'srbtranslatin' ),
                'options'     => array(
                    'website' => \__( 'Entire website', 'srbtranslatin' ),
                    'content' => \__( 'Content only', 'srbtranslatin' ),
                ),
            ),
        ),
        array(
            'id'      => 'fix_permalinks',
            'type'    => 'checkbox',
            'title'   => \__( 'Fix Permalinks', 'srbtranslatin' ),
            'section' => 'advanced',
            'extras'  => array(
                'default'         => false,
                'description'     => $disable_permalinks
                    ? \sprintf(
                        \__( 'Fixes permalinks for cyrillic scripts. This option is currently disabled because your current locale is set to %s which will automatically change permalinks.', 'srbtranslatin' ),
                        $locale,
                    )
                    : \__( 'Fixes permalinks for cyrillic scripts', 'srbtranslatin' ),
                'html_attributes' => array(
                    'disabled' => $disable_permalinks,
                ),
            ),
        ),
        array(
            'id'      => 'fix_search',
            'type'    => 'checkbox',
            'title'   => \__( 'Fix Search', 'srbtranslatin' ),
            'section' => 'advanced',
            'extras'  => array(
                'default'     => true,
                'description' => \__( 'Enables searching cyrillic content via latin script', 'srbtranslatin' ),
            ),
        ),
        array(
            'id'      => 'fix_ajax',
            'type'    => 'checkbox',
            'title'   => \__( 'Fix Ajax', 'srbtranslatin' ),
            'section' => 'advanced',
            'extras'  => array(
                'default'     => false,
                'description' => \__( 'Transliterates ajax calls', 'srbtranslatin' ),
            ),
        ),
        array(
            'id'      => 'fix_titles',
            'type'    => 'checkbox',
            'title'   => \__( 'Fix Titles', 'srbtranslatin' ),
            'section' => 'advanced',
            'extras'  => array(
                'default'     => false,
                'description' => \__( 'Fixes titles for cyrillic scripts', 'srbtranslatin' ),
            ),
        ),
    ),
);
