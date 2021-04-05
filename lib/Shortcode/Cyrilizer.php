<?php

namespace SGI\STL\Shortcode;

use function SGI\STL\Utils\{
    get_script,
    get_origin_script,
    is_serbian
};

class Cyrilizer
{

    use Transliterable;

    public function __construct()
    {

        add_shortcode('stl_cyr', [&$this, 'shortcodeCallback']);
        add_shortcode('stl_cyrillic', [&$this, 'shortcodeCallback']);

    }

    public function shortcodeCallback($atts, $content)
    {

        $origin_script = get_origin_script();
        $script = get_script();

        if ($origin_script == $script || !is_serbian())
            return $content;

        $uuid = uniqid();

        self::$shortcodes[$uuid] = $content;

        return $uuid;

    }

}