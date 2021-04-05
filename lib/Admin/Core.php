<?php

namespace SGI\STL\Admin;

use const SGI\STL\{
    BASENAME,
    PATH
};

use function SGI\STL\Utils\getOptions;

use Turanjanin\SerbianTransliterator\Transliterator;

class Core
{

    private $opts;

    public function __construct()
    {

        $this->opts = getOptions();

        // Add action link
        add_filter('plugin_action_links_' . BASENAME, [&$this,'pluginLinks'], 20, 1);

        // Add meta links
        add_filter('plugin_row_meta', [&$this,'pluginMeta'], 20, 2);

        // Add Menu pages
        add_action('admin_menu', [&$this, 'addMenuPages'], 10);


        //Filename transliteration
        add_filter('sanitize_file_name', array(&$this, 'sanitizeFilename'),50,2);
        add_filter('sanitize_title', [&$this, 'sanitizeTitle'], 100, 2);

    }

    public function pluginLinks($links)
    {

        $links[] = sprintf(
            '<a href="%s">%s</a>',
            admin_url('admin.php?page=stl_settings'),
            __('Settings', 'SrbTransLatin')
        );

        return $links;

    }

    public function pluginMeta($plugin_meta, $plugin_file)
    {

        if ($plugin_file != 'srbtranslatin/srbtranslatin.php') :
            return $plugin_meta;
        endif;

        $plugin_meta[] = sprintf(
            '<a href="%s" target="_blank">%s</a>',
            'https://sgi.io/plugins/srbtranslatin',
            __('Documentation', 'SrbTransLatin')
        );

        $plugin_meta[] = sprintf(
            '<a href="%s" target="_blank"><strong>%s</strong></a>',
            'https://paypal.me/seebeen',
            __('Donate', 'SrbTransLatin')
        );

        return $plugin_meta;

    }

    public function addMenuPages()
    {

        $image = file_get_contents(PATH . 'assets/img/stl-logo.svg');

        add_menu_page(
            __('Latinisation', 'SrbTransLatin'),
            __('Latinisation', 'SrbTransLatin'),
            'manage_options',
            'stl',
            function(){},
            'data:image/svg+xml;base64,'.base64_encode($image),
            99
        ); 

        add_submenu_page(
            'stl',
            __('Settings - SrbTransLatin', 'SrbTransLatin'),
            __('Settings', 'SrbTransLatin'),
            'manage_options',
            'stl_settings',
            [&$this, 'settings_page']
        );

        remove_submenu_page('stl', 'stl');

    }

    public function settings_page()
    {
        
        printf(
            '<h1>%s</h1>',
            get_admin_page_title()
        );

        echo '<form method="POST" action="options.php">';

        settings_fields('stl_settings');
        do_settings_sections('stl_settings');
        submit_button();

        echo '</form>';
    }

    public function sanitizeFilename($filename, $filename_raw)
    {

        if (!$this->opts['file']['names'])
            return $filename;

        $filename = Transliterator::toAsciiLatin($filename);

        return $filename;

    }

    public function sanitizeTitle($title, $fallback_title)
    {

        if (!$this->opts['fixes']['permalinks'])
            return $title;

        return Transliterator::toAsciiLatin(($title));

    }


}