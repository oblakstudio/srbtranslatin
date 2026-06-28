<?php //phpcs:disable SlevomatCodingStandard.Functions.RequireMultiLineCall.RequiredMultiLineCall
/**
 * Settings page schema.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$site_locale                 = \array_key_exists( 'stl_test_locale', $GLOBALS ) ? (string) $GLOBALS['stl_test_locale'] : \get_locale();
$locale_handles_permalinks   = \in_array( $site_locale, array( 'sr_RS', 'bs_BA' ), true );
$media_runtime_available     = true;
$permalink_runtime_available = false;
$disable_permalinks          = $locale_handles_permalinks || ! $permalink_runtime_available;
$navigation_menus            = \array_key_exists( 'stl_test_nav_menus', $GLOBALS ) ? (array) $GLOBALS['stl_test_nav_menus'] : \get_registered_nav_menus();
$has_navigation_menus        = 0 < \count( $navigation_menus );

return array(
    'page'     => array(
        'option_name' => \STL\Common\Settings\Settings_Schema::OPTION_NAME,
        'slug'        => \STL\Common\Settings\Settings_Schema::PAGE_SLUG,
        'page_title'  => \__( 'Latinisation', 'srbtranslatin' ),
        'menu_title'  => \__( 'Latinisation', 'srbtranslatin' ),
        'parent_slug' => 'options-general.php',
        'capability'  => 'manage_options',
    ),
    'tabs'     => array(
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
    'fields'   => array(
        array(
            'id'      => 'enabled_scripts',
            'type'    => 'buttons_group',
            'title'   => \__( 'Enabled scripts', 'srbtranslatin' ),
            'section' => 'general',
            'extras'  => array(
                'default'     => 'both',
                'description' => \__( 'Choose between Latin-only mode and Cyrillic / Latin mode.', 'srbtranslatin' ),
                'options'     => array(
                    'lat'  => 'Ć ' . \__( 'Latin', 'srbtranslatin' ),
                    'both' => 'Ć Ћ ' . \__( 'Cyrillic / Latin', 'srbtranslatin' ),
                ),
            ),
        ),
        array(
            'id'      => 'default_script',
            'type'    => 'buttons_group',
            'title'   => \__( 'Default script', 'srbtranslatin' ),
            'section' => 'general',
            'extras'  => array(
                'default'     => 'cir',
                'description' => \__( 'Default script used for the website if user did not select a script', 'srbtranslatin' ),
                'options'     => array(
                    'cir' => 'Ћ ' . \__( 'Cyrillic', 'srbtranslatin' ),
                    'lat' => 'Ć ' . \__( 'Latin', 'srbtranslatin' ),
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
            'id'      => 'extend_ls',
            'type'    => 'checkbox',
            'title'   => \__( 'Extend WPML language switcher', 'srbtranslatin' ),
            'section' => 'general',
            'extras'  => array(
                'default'     => false,
                'description' => \__( 'Split Serbian into separate Cyrillic and Latin entries inside the WPML language switcher', 'srbtranslatin' ),
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
            'id'      => 'media_warning',
            'type'    => 'description',
            'title'   => '',
            'section' => 'media',
            'extras'  => array(
                'description'     => ! $media_runtime_available
                    ? '<strong>' . \__( 'Media filename transliteration options are legacy settings and are not active in the current src runtime yet.', 'srbtranslatin' ) . '</strong>'
                    : '',
                'html_attributes' => array(
                    'class' => 'notice inline notice-warning',
                ),
            ),
        ),
        array(
            'id'      => 'transliterate_uploads',
            'type'    => 'checkbox',
            'title'   => \__( 'Transliterate uploads', 'srbtranslatin' ),
            'section' => 'media',
            'extras'  => array(
                'default'         => true,
                'description'     => \__( 'Transliterate filenames on upload', 'srbtranslatin' ),
                'html_attributes' => array(),
            ),
        ),
        array(
            'id'      => 'separate_uploads',
            'type'    => 'checkbox',
            'title'   => \__( 'Script specific filenames', 'srbtranslatin' ),
            'section' => 'media',
            'extras'  => array(
                'default'         => true,
                'description'     => \__( 'Check this box if you want to have separate filenames for each script', 'srbtranslatin' ),
                'html_attributes' => array(),
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
                'default'         => 'website',
                'description'     => \__( 'Choose if you want to limit the script specific filenames on the entire website, or in content only', 'srbtranslatin' ),
                'options'         => array(
                    'website' => \__( 'Entire website', 'srbtranslatin' ),
                    'content' => \__( 'Content only', 'srbtranslatin' ),
                ),
                'html_attributes' => array(),
            ),
        ),
        array(
            'id'      => 'fix_permalinks',
            'type'    => 'checkbox',
            'title'   => \__( 'Fix Permalinks', 'srbtranslatin' ),
            'section' => 'advanced',
            'extras'  => array(
                'default'         => false,
                'description'     => ! $permalink_runtime_available
                    ? (
                        $locale_handles_permalinks
                            ? \sprintf(
                                // Translators: %s is replaced with the current site locale, e.g. "sr_RS". Do not translate the locale itself.
                                \__( 'Permalink transliteration is a legacy option and is not active in the current src runtime yet. It also remains disabled because your current locale is set to %s, which already changes permalinks automatically.', 'srbtranslatin' ),
                                $site_locale,
                            )
                            : \__( 'Permalink transliteration is a legacy option and is not active in the current src runtime yet.', 'srbtranslatin' )
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
