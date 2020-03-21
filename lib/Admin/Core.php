<?php

namespace SGI\STL\Admin;

use const SGI\STL\{
    BASENAME,
    VERSION,
    DOMAIN
};

use function SGI\STL\Core\Utils\get_stl_config;

use SGI\Transliterator;

class Core
{

    private $opts;

    public function __construct()
    {

        $this->opts = get_stl_config();

        // Add action link
        add_filter('plugin_action_links_'.BASENAME, [&$this,'plugin_links'], 20, 3);

        // Add meta links
        add_filter('plugin_row_meta', [&$this,'plugin_meta'], 20, 4);

        //Filename transliteration
        add_filter('sanitize_file_name', array(&$this, 'sanitize_file_name'),50,2);

    }

    public function plugin_links($links, $plugin_file, $plugin_data)
    {

        $links[] = sprintf(
            '<a href="%s">%s</a>',
            admin_url('admin.php?page=stl_settings'),
            __('Settings', DOMAIN)
        );

        return $links;

    }

    public function plugin_meta($plugin_meta, $plugin_file, $plugin_data, $status)
    {

        if ($plugin_file != 'srbtranslatin/srbtranslatin.php')
            return $plugin_meta;

        $plugin_meta[] = sprintf(
            '<a href="%s" target="_blank">%s</a>',
            'https://sgi.io/plugins/srbtranslatin',
            __('Documentation', DOMAIN)
        );

        $plugin_meta[] = sprintf(
            '<a href="%s" target="_blank"><strong>%s</strong></a>',
            'https://paypal.me/seebeen',
            __('Donate', DOMAIN)
        );

        return $plugin_meta;

    }

    public function sanitize_file_name($filename, $filename_raw)
    {

        if (!$this->opts['file']['names'])
            return $filename;

        $filename = Transliterator::cir_to_cut_lat($filename);

        return $filename;

    }


}