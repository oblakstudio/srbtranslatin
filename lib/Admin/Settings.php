<?php

namespace SGI\STL\Admin;

use SGI\STL\Admin\Settings as Settings;

use function SGI\STL\Core\Utils\{
    get_stl_config,
    is_wpml_active
};

use const SGI\STL\DOMAIN;

class Settings
{

    use Settings\Generator,
        Settings\General,
        Settings\Files,
        Settings\Menu,
        Settings\Multilanguage,
        Settings\Fixes;
        


    private $opts;

    private $expert_enable;

    public function __construct()
    {

        $this->opts = get_stl_config();

        /**
         * Enables changing of expert settings
         *
         * @since 2.0.0
         *
         * @param expert_enable - Flag which enables expert settings
         */
        $expert_enable = apply_filters("sgi/stl/settings/expert", false);

        $this->expert_enable = $expert_enable;

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

        $prev_config = get_stl_config();

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

        $expert_settings = [
            'cookie',
            'names',
            'delim',
            'permalinks'
        ];

        foreach ($checkboxes as $section => $array) :

            foreach ($array as $opt) :



                if (in_array($opt, $expert_settings)) :

                    if ($this->expert_enable) :

                        $opts[$section][$opt] = (isset($opts[$section][$opt])) ? true : false;

                    else : 

                        $opts[$section][$opt] = $prev_config[$section][$opt];

                    endif;

                else :

                    $opts[$section][$opt] = (isset($opts[$section][$opt])) ? true : false;

                endif;

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

        if (!$this->expert_enable) :

            $opts['file']['delim'] = $prev_config['file']['delim'];

        endif;

        $opts['core']['versions'] = [
            '2.0'
        ];

        return $opts;

    }

}