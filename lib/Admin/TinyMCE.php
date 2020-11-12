<?php

namespace SGI\STL\Admin;

use const SGI\STL\BASENAME;

class TinyMCE
{

    public function __construct()
    {

        add_filter('admin_head', [&$this,'add_tinymce_vars']);
        add_filter('mce_external_plugins', [&$this, 'mce_external_plugins'],10,1);
        add_filter('mce_buttons', [&$this, 'mce_buttons'],10,1);

    }

    public function mce_external_plugins($plugin_array)
    {

        $plugin_array['stl_cyr'] = plugins_url( "assets/js/tinymce/stl_cyr.js", BASENAME );

        return $plugin_array;

    }

    public function mce_buttons($buttons)
    {

        array_push($buttons, 'stl_cyr');

        return $buttons;

    }

    public function add_tinymce_vars()
    {

        printf(
            '<script type="text/javascript">
                var stl = {
                    button_icon: "%s",
                    title: "%s",
                    label: "%s",
                    cyr: "%s",
                    show: "%s",
                    translit: "%s"
                }
            </script>',
            plugins_url( "assets/img/stl-cyr.png", BASENAME),
            __('SrbTransLatin - shortcodes', 'SrbTransLatin'),
            __('Select a shortcode', 'SrbTransLatin'),
            __('Forced Cyrillic', 'SrbTransLatin'),
            __('Selective output', 'SrbTransLatin'),
            __('Special transliteration', 'SrbTransLatin')
        );

    }

}