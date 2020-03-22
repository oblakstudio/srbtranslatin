<?php

namespace SGI\STL\Admin;

use const SGI\STL\{
    PATH,
    DOMAIN
};

class Pages
{

    public function __construct()
    {

        add_action('admin_menu', [&$this, 'add_menu_pages'], 10);

    }

    private function get_menu_image()
    {
        
        $image = file_get_contents(PATH . 'assets/img/stl-logo.svg');

        return 'data:image/svg+xml;base64,'.base64_encode($image);

    }

    public function add_menu_pages()
    {

        add_menu_page(
            __('Latinisation', DOMAIN),
            __('Latinisation', DOMAIN),
            'manage_options',
            'stl',
            function(){},
            $this->get_menu_image(),
            99
        ); 

        add_submenu_page(
            'stl',
            __('Settings - SrbTransLatin', DOMAIN),
            __('Settings', DOMAIN),
            'manage_options',
            'stl_settings',
            [&$this, 'settings_page']
        );

        // add_submenu_page(
        //     'stl',
        //     __('System Status - SrbTransLatin', DOMAIN),
        //     __('System Status', DOMAIN),
        //     'manage_options',
        //     'stl_status',
        //     [&$this, 'settings_page']
        // );

        remove_submenu_page('stl', 'stl');

    }

    public function settings_page()
    {

        $template_file = PATH."templates/settings/page.php";

        include $template_file;

    }

}