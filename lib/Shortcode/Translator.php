<?php

namespace SGI\STL\Shortcode;

use function SGI\STL\Utils\{
    get_script,
    get_origin_script,
};

class Translator
{

    use Transliterable;

    public function __construct()
    {

        add_shortcode('stl_translit', [&$this, 'shortcodeCallback']);

    }

    public function shortcodeCallback($atts, $content)
    {

        shortcode_atts([
            'latin' => ''
        ], $atts);

        $script = get_script();
        $origin_script = get_origin_script();

        if ($origin_script == $script)
            return $content;

        $uuid = uniqid();

        self::$shortcodes[$uuid] = $atts['latin'];

        return $uuid;

    }

}