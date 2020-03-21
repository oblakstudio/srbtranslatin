<?php

namespace SGI\STL\Admin\Settings;

use const SGI\STL\DOMAIN;

trait Multilanguage
{

    public function section_ml()
    {

        add_settings_section(
            'stl_settings_ml',
            __('Multilanguage Settings', DOMAIN),
            [&$this, 'callback_section_ml'],
            'stl_settings'
        );

    }

    public function callback_section_ml()
    {

        printf(
            '<p>%s</p>',
            __('Multilanguage settings control settings for language switchers', DOMAIN)
        );

        add_settings_field(
            'stl_ml_wpml',
            __('WPML Settings', DOMAIN),
            [&$this, 'callback_option_wpml'],
            'stl_settings',
            'stl_settings_ml',
            $this->opts['ml']['wpml']
        );

    }

    public function callback_option_wpml($wpml)
    {

        self::checkbox(
            $wpml,
            'sgi/stl/opt[ml][wpml]',
            true,
            __('Extend WPML Language Switcher', DOMAIN),
            __('Enable if you want to enable script selector in WPML Language Switcher', DOMAIN)
        );

    }

}