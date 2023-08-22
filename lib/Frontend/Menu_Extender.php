<?php
/**
 * Menu_Extender class file
 *
 * @package SrbTransLatin
 */

namespace Oblak\STL\Frontend;

use stdClass;

/**
 * Extends the navigation menus with the plugin's functionality
 *
 * @since 3.0.0
 */
class Menu_Extender {
    /**
     * Class constructor
     */
    public function __construct() {
        add_filter( 'wp_get_nav_menu_items', array( $this, 'add_script_selector_to_menu' ), 10, 2 );
    }

    /**
     * Adds custom script selector to the menu
     *
     * @param  stdClass[] $items Menu items.
     * @param  stdClass   $menu  Menu object.
     * @return stdClass[]        Modified menu items.
     */
    public function add_script_selector_to_menu( $items, $menu ) {
        $locations = get_nav_menu_locations();
        $found     = false;

        foreach ( $locations as $location => $term_id ) {

            if (
                $term_id === $menu->term_id &&
                STL()->get_settings( 'menu', 'extend_menu' ) === $location &&
                STL()->get_settings( 'menu', 'extend' ) &&
                STL()->manager->is_serbian() &&
                ! STL()->ml->ml_plugin_active()
            ) {
                $found = true;
            }
        }

        if ( ! $found ) {
            return $items;
        }

        $menu_order = 2000;
        $current    = 2000;

        switch ( STL()->get_settings( 'menu', 'selector_type' ) ) {
            case 'submenu':
                $menu_items = $this->generate_submenu( $menu_order, $current );
                break;
            case 'inline':
                $menu_items = $this->generate_inline( $menu_order, $current );
                break;
        }

        return array_merge( $items, $menu_items );
    }

    /**
     * Generates a submenu script selector
     *
     * @param  int $menu_order Menu item order.
     * @param  int $current    Menu item current.
     * @return stdClass[]      Menu items.
     */
    public function generate_submenu( $menu_order, $current ) {
        $root_item = $this->create_menu_item(
            STL()->get_settings( 'menu', 'menu_title' ),
            '#',
            ++$menu_order,
            0,
            ++$current
        );

        return array_merge(
            array( $root_item ),
            $this->generate_inline( $menu_order, $current, $root_item->ID )
        );
    }

    /**
     * Generates an inline script selector
     *
     * @param  int $menu_order  Menu item order.
     * @param  int $current     Menu item current.
     * @param  int $menu_parent Menu item parent.
     */
    public function generate_inline( $menu_order, $current, $menu_parent = 0 ) {
        $menu_items = array();

        foreach ( stl_get_available_scripts() as $id => $title ) {
            $menu_items[] = $this->create_menu_item(
                $title,
                add_query_arg(
                    array(
                        STL()->manager->get_url_param() => $id,
                    ),
                    stl_get_current_url()
                ),
                ++$menu_order,
                $menu_parent,
                ++$current
            );
        }

        return $menu_items;
    }

    /**
     * Creates a menu item
     *
     * @param  string $title       Menu item title.
     * @param  string $url         Menu item URL.
     * @param  int    $order       Menu item order.
     * @param  int    $menu_parent Menu item parent.
     * @param  int    $current     Menu item current.
     * @return stdClass            Menu item object.
     */
    private function create_menu_item( $title, $url, $order, $menu_parent = 0, $current ) {
        $item = new stdClass();

        $item->ID               = 100000 + $order + $menu_parent;
        $item->db_id            = $item->ID;
        $item->title            = $title;
        $item->url              = $url;
        $item->menu_order       = $order;
        $item->menu_item_parent = $menu_parent;
        $item->type             = '';
        $item->object           = '';
        $item->object_id        = '';
        $item->classes          = array();
        $item->target           = '';
        $item->attr_title       = '';
        $item->description      = '';
        $item->xfn              = '';
        $item->status           = '';
        $item->current          = $current;

        return wp_setup_nav_menu_item( $item );
    }
}
