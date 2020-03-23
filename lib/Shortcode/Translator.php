<?php

namespace SGI\STL\Shortcode;

use function SGI\STL\Core\Utils\get_script;

class Translator implements Shortcode
{

    use Transliterable,
        Legacy;

    public function __construct()
    {

        $this->name = 'stl_replace';

        add_shortcode('stl_translit', [&$this, 'shortcode_callback']);
        add_shortcode('stl_replace',  [&$this, 'legacy_callback']);

    }

    public function shortcode_callback($atts, $content)
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