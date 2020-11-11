<?php

namespace SGI\STL\Admin;

use SGI\STL\Admin\Settings as Settings;

use function SGI\STL\Utils\{
    getOptions,
    is_wpml_active
};

use const SGI\STL\DOMAIN;

class SettingsPage
{

    use Settings\Generator,
        Settings\General,
        Settings\Files,
        Settings\Menu,
        Settings\Multilanguage,
        Settings\Fixes;
        


    private $opts;



    public function __construct()
    {

        $this->opts = getOptions();


        add_action('admin_init', [&$this, 'register_settings']);

    }

    public function register_settings()
    {

        register_setting(
            'stl_settings',
            'sgi/stl/opt',
           [&$this, 'sanitize_opts']
        );

        $this->section_general();

        $this->section_files();

        //$this->section_menu();

        if (is_wpml_active())
            $this->section_ml();

        $this->section_fixes();

    }

    public function sanitize_opts($opts)
    {

        $prev_config = getOptions();

        $checkboxes = [
            'core' => [
                'cookie',
            ],
            'file' => [
                'names',
                'translit',
                'content'
            ],
            'menu' =>[
                'extend'
            ],
            'fixes' => [
                'permalinks',
                'search'
            ],
            'ml'    => [
                'wpml'
            ]
        ];


        foreach ($checkboxes as $section => $array) :

            foreach ($array as $opt) :

                    $opts[$section][$opt] = (isset($opts[$section][$opt])) ? true : false;

                if ($opt == 'permalinks') :

                    $wplang = get_locale();
                    $already_cyrillic = ( ($wplang == 'sr_RS') || ($wplang == 'bs_BA') ) ? true : false;

                    if ($already_cyrillic) :

                        $opts[$section][$opt] = false;

                    else :

                        $opts[$section][$opt] = (isset($opts[$section][$opt])) ? true : false;

                    endif;

                endif;

            endforeach;

        endforeach;

            $opts['file']['delim'] = $prev_config['file']['delim'];

        return $opts;

    }

}