<?php

namespace SGI\STL\Core;

use SGI\STL\{
    Update    as Update,
    Admin     as Admin,
    Ajax      as Ajax,
    Frontend  as Frontend,
    Shortcode as Shortcode,
    Widget    as Widget
};

use const SGI\STL\{
    FILE,
    PATH,
    DOMAIN
};

class Bootstrap
{

    public function __construct()
    {
        
        add_action('wp_loaded', [&$this, 'load_textdomain']);

        if (is_admin()) :
            add_action('init', [&$this, 'load_admin']);
        endif;

        if (wp_doing_ajax()) :
            add_action('init', [&$this, 'load_ajax']);
        endif;

        add_action('init', [&$this, 'load_frontend']);
        add_action('widgets_init', [&$this, 'load_widgets']);

    }

    public function load_textdomain()
    {

        $domain_path = basename(dirname(FILE)).'/languages';

        load_plugin_textdomain(
            DOMAIN,
            false,
            $domain_path
        );

    }

    public function load_admin()
    {

        new Update\Handler();

        new Admin\Core();
        new Admin\Pages();
        new Admin\Scripts();
        new Admin\Settings();

        new Admin\TinyMCE();

    }

    public function load_ajax()
    {

    }

    public function load_frontend()
    {

        new Frontend\Core();

        new Shortcode\Info();
        new Shortcode\Cyrilizer();
        new Shortcode\Translator();
        new Shortcode\Selective_Output();

    }

    public function load_widgets()
    {

        register_widget('SGI\STL\Widget\Selector');

    }

}