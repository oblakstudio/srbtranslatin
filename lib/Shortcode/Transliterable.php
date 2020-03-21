<?php

namespace SGI\STL\Shortcode;

trait Transliterable
{

    private static $shortcodes = [];

    public static function get_shortcodes()
    {
        return self::$shortcodes;
    }

}