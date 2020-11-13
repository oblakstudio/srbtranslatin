<?php

namespace SGI\STL\Shortcode;

use function SGI\STL\Utils\get_script;

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

        if ($script == 'cir')
            return $content;

        $uuid = uniqid();

        self::$shortcodes[$uuid] = $atts['latin'];

        return $uuid;

    }

}