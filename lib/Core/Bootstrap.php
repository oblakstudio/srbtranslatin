<?php

namespace SGI\STL\Core;

use SGI\STL\{
    Update    as Update,
    Admin     as Admin,
    Ajax      as Ajax,
    Frontend  as Frontend,
    Shortcode as Shortcode,
};

use const SGI\STL\{
    FILE,
    PATH,
    DOMAIN
};

/**
 * Main plugin class which loads all needed frontend and backend functionalities
 */
class Bootstrap
{

    /**
     * @var Bootstrap Class instance
     */
    private static $instance = null;

    private function __construct()
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

    public static function getInstance()
    {

        if (self::$instance == null) :
            self::$instance = new self;
        endif;

        return self::$instance;

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
        new Admin\Scripts();
        new Admin\SettingsPage();

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
        new Shortcode\SelectiveOutput();
        new Shortcode\LegacyShortcodes();

    }

    public function load_widgets()
    {

        register_widget('SGI\STL\Widget\Selector');

    }

}