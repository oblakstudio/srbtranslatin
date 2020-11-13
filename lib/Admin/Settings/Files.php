<?php

namespace SGI\STL\Admin\Settings;

use const SGI\STL\DOMAIN;

trait Files
{

    public function section_files()
    {

        add_settings_section(
            'stl_settings_file',
            __('File and Media Settings', 'SrbTransLatin'),
            [&$this, 'callback_section_files'],
            'stl_settings'
        );

        add_settings_field(
            'stl_file_names',
            __('Fix Filenames', 'SrbTransLatin'),
            [&$this, 'callback_option_names'],
            'stl_settings',
            'stl_settings_file',
            $this->opts['file']['names']
        );

        add_settings_field(
            'stl_file_translit',
            __('Latin speficic filenames', 'SrbTransLatin'),
            [&$this, 'callback_option_translit'],
            'stl_settings',
            'stl_settings_file',
            [
                'translit' => $this->opts['file']['translit'],
                'names'    => $this->opts['file']['content']
            ]
        );

        add_settings_field(
            'stl_file_delim',
            __('Filename delimiter', 'SrbTransLatin'),
            [&$this, 'callback_option_delim'],
            'stl_settings',
            'stl_settings_file',
            $this->opts['file']['delim']
        );

        // add_settings_field(
        //     'stl_core_content',
        //     __('Cookie', 'SrbTransLatin'),
        //     [&$this, 'callback_option_content'],
        //     'stl_settings',
        //     'stl_settings_file',
        //     $this->opts['file']['content']
        // );

    }

    public function callback_section_files()
    {

        printf(
            '<p>%s</p>',
            __('File & Media settings control filename transliteration and media saving', 'SrbTransLatin')
        );

    }

    public function callback_option_names($names)
    {

        self::checkbox(
            $names,
            'sgi/stl/opt[file][names]',
            true,
            __('Transliterate filenames', 'SrbTransLatin'),
            __('Enable if you want to convert cyrillic filenames to latin', 'SrbTransLatin')
        );

    }

    public function callback_option_translit($opts)
    {

        self::checkbox(
            $opts['translit'],
            'sgi/stl/opt[file][translit]',
            true,
            __('Script specific filenames', 'SrbTransLatin'),
            __('Enable if you want to have cyrillic and latin versions of an image ', 'SrbTransLatin')
        );

        echo '<div class="stl-content-only" style="margin-top:20px;">';

        self::checkbox(
            $opts['names'],
            'sgi/stl/opt[file][content]',
            true,
            __('Script specific filenames in content only', 'SrbTransLatin'),
            __('Enable if you want to limit different filenames to post/page content only', 'SrbTransLatin')
        );

        echo '</div>';

    }

    public function callback_option_delim($delim)
    {

        self::input(
            $delim,
            'sgi/stl/opt[file][delim]',
            false,
            '',
            __('Filename delimiter - i.e. - myfilename-cir.jpg', 'SrbTransLatin')
        );

    }
    
}