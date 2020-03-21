<?php

namespace SGI\STL\Admin;

use const \SGI\STL\{
    BASENAME,
    VERSION,
    DOMAIN,
    PATH
};

class Scripts
{

    private $styles;

    private $scripts;

    public function __construct()
    {

        $this->styles = [
            // 'vendor-css' => 'css/vendor.min.css',
            'main-css'   => 'css/main.min.css'
        ];

        $this->scripts = [
            'vendor-js' => 'js/vendor.min.js',
            'main-js'   => 'js/main.min.js'
        ];

        global $pagenow;

        $page = $_GET['page'] ?? '';

        if ( ($pagenow == 'admin.php') && (in_array($page, ['stl_settings', 'stl_system_status'])) ) :

            add_action('admin_enqueue_scripts', [&$this,'add_styles']);

            //add_action('admin_enqueue_scripts', [&$this,'add_scripts']);

        endif;

    }

    public static function assets_uri($file)
    {

        return plugins_url("assets/{$file}",BASENAME);

    }

    public function add_styles()
    {

        foreach ($this->styles as $name => $file) :

            $handler = DOMAIN . $name;

            wp_register_style($handler, self::assets_uri($file), null, VERSION );
            wp_enqueue_style($handler);

        endforeach;

    }

    public function add_scripts()
    {

        foreach ($this->scripts as $name => $file) :

            $handler = DOMAIN . $name;

            wp_register_script($handler, self::assets_uri($file), array('jquery'), VERSION, true);
            wp_enqueue_script($handler);

        endforeach;

    }


}