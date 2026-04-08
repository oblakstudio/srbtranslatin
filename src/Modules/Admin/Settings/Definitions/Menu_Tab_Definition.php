<?php
/**
 * Menu tab definition.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Admin\Settings\Definitions;

use STL\Admin\Settings\Admin_Context;

final class Menu_Tab_Definition implements Settings_Tab_Definition {
    public function __construct(
        private Admin_Context $context,
    ) {
    }

    public function build(): array {
        $navigation_menus = $this->context->get_registered_nav_menus();
        $has_nav_menus    = 0 < \count( $navigation_menus );

        return array(
            'tab'     => array(
                'id'    => 'menu',
                'title' => \__( 'Menus', 'srbtranslatin' ),
                'icon'  => 'dashicons-menu',
            ),
            'section' => array(
                'id'          => 'menu',
                'title'       => \_x( 'Navigation menu settings', 'section name', 'srbtranslatin' ),
                'description' => \__( 'Menu settings control extending and tweaking the script selector in theme menus', 'srbtranslatin' ),
                'tab'         => 'menu',
            ),
            'fields'  => array(
                array(
                    'id'      => 'menu_warning',
                    'type'    => 'description',
                    'title'   => '',
                    'section' => 'menu',
                    'extras'  => array(
                        'description'     => ! $has_nav_menus
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
                            'disabled' => ! $has_nav_menus,
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
                            'disabled' => ! $has_nav_menus,
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
                            'disabled' => ! $has_nav_menus,
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
                            'disabled' => ! $has_nav_menus,
                        ),
                    ),
                ),
            ),
        );
    }
}
