<?php

namespace SGI\STL\Admin\Settings;

use const SGI\STL\DOMAIN;

trait General
{

    public function section_general()
    {

        add_settings_section(
            'stl_settings_core',
            __('General Settings', 'SrbTransLatin'),
            [&$this, 'callback_section_core'],
            'stl_settings'
        );

        add_settings_field(
            'stl_core_origin_script',
            __('Original script', 'SrbTransLatin'),
            [&$this, 'callback_option_origin_script'],
            'stl_settings',
            'stl_settings_core',
            $this->opts['core']['origin_script']
        );

        add_settings_field(
            'stl_core_script',
            __('Default script', 'SrbTransLatin'),
            [&$this, 'callback_option_script'],
            'stl_settings',
            'stl_settings_core',
            $this->opts['core']['script']
        );

        add_settings_field(
            'stl_core_param',
            __('URL Parameter', 'SrbTransLatin'),
            [&$this, 'callback_option_param'],
            'stl_settings',
            'stl_settings_core',
            $this->opts['core']['param']
        );

    }

    public function callback_section_core()
    {

        printf(
            '<p>%s</p>',
            __('General settings control main functionality of the plugin', 'SrbTransLatin')
        );

    }

    public function callback_option_origin_script($script)
    {

        $options = array(
            'cir' => __('Cyrillic', 'SrbTransLatin'),
            'lat' => __('Latin', 'SrbTransLatin')
        );

        Generator::select(
            $options,
            $script,
            'sgi/stl/opt[core][origin_script]',
            true,
            '',
            __('Main script used on the website', 'SrbTransLatin'),
            ''
        );

    }

    public function callback_option_script($script)
    {

        $options = array(
            'cir' => __('Cyrillic', 'SrbTransLatin'),
            'lat' => __('Latin', 'SrbTransLatin')
        );

        Generator::select(
            $options,
            $script,
            'sgi/stl/opt[core][script]',
            true,
            '',
            __('Default script used for the website if user did not select a script', 'SrbTransLatin'),
            ''
        );

    }

    public function callback_option_cookie($cookie)
    {

        Generator::checkbox(
            $cookie,
            'sgi/stl/opt[core][cookie]',
            true,
            __('Use Cookie', 'SrbTransLatin'),
            __('Enable if you want to keep script preference setting in a user cookie', 'SrbTransLatin')
        );

    }

    public function callback_option_param($param)
    {

        Generator::input(
            $param,
            'sgi/stl/opt[core][param]',
            true,
            '',
            __('URL parameter used for script selector', 'SrbTransLatin')
        );

    }

}