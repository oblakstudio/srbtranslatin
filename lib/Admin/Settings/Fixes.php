<?php

namespace SGI\STL\Admin\Settings;

trait Fixes
{

    public function section_fixes()
    {

        add_settings_section(
            'stl_settings_fixes',
            __('Advanced Settings', 'SrbTransLatin'),
            [&$this, 'callback_section_fixes'],
            'stl_settings'
        );
        
    }

    public function callback_section_fixes()
    {

        printf(
            '<p>%s</p>',
            __('Advanced settings control permalink and search settings', 'SrbTransLatin')
        );

        add_settings_field(
            'stl_fixes_permalinks',
            __('Fix Permalinks', 'SrbTransLatin'),
            [&$this, 'callback_option_permalinks'],
            'stl_settings',
            'stl_settings_fixes',
            $this->opts['fixes']['permalinks']
        );

        add_settings_field(
            'stl_fixes_search',
            __('Fix Search', 'SrbTransLatin'),
            [&$this, 'callback_option_search'],
            'stl_settings',
            'stl_settings_fixes',
            $this->opts['fixes']['search']
        );

        add_settings_field(
            'stl_fixes_ajax',
            __('Fix Ajax', 'SrbTransLatin'),
            [&$this, 'callback_option_ajax'],
            'stl_settings',
            'stl_settings_fixes',
            $this->opts['fixes']['ajax']
        );

    }

    public function callback_option_permalinks($permalinks)
    {

        $wplang = get_locale();

        $already_cyrillic = ( ($wplang == 'sr_RS') || ($wplang == 'bs_BA') ) ? true : false;

        self::checkbox(
            $permalinks,
            'sgi/stl/opt[fixes][permalinks]',
            !$already_cyrillic,
            __('Transliterate permalinks to latin script', 'SrbTransLatin'),
            __('Enable if you want to change cyrillic permalinks to latin', 'SrbTransLatin')
        );

        if ($already_cyrillic) :

            printf(
                '<p class="description"><strong>%s</strong></p>',
                __(sprintf(
                    'This option is currently disabled because your current locale is set to %s which will automatically change permalnks',
                    $wplang
                ), 'SrbTransLatin')
            );

        endif;

    }

    public function callback_option_search($search)
    {

        self::checkbox(
            $search,
            'sgi/stl/opt[fixes][search]',
            true,
            __('Enable search using latin script', 'SrbTransLatin'),
            __('Enables searching for posts using both cyrillic and latin script', 'SrbTransLatin')
        );

    }

    public function callback_option_ajax($ajax)
    {

        self::checkbox(
            $ajax,
            'sgi/stl/opt[fixes][ajax]',
            true,
            __('Transliterate ajax calls', 'SrbTransLatin'),
            __('Enable if you want to transliterate ajax calls on your website', 'SrbTransLatin')
        );

        printf(
            '<p class="description"><strong>%s</strong>%s</p>',
            __('This functionality is in BETA. Test your website thoroughly when enabling.', 'SrbTransLatin'),
            __('If you have problems, open a support ticket', 'SrbTransLatin')
        );

    }

}