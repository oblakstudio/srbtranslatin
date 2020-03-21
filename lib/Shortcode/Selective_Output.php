<?php

namespace SGI\STL\Shortcode;

use function SGI\STL\Core\Utils\get_script;

class Selective_Output implements Shortcode, Multicode
{

    public function __construct()
    {

        add_shortcode('stl_is_cyrillic', [&$this, 'multicode_callback']);
        add_shortcode('stl_is_latin', [&$this, 'multicode_callback']);
        add_shortcode('stl_show', [&$this, 'shortcode_callback']);

    }

    public function multicode_callback($atts, ?string $content, ?string $shortcode)
    {

        $atts = ($shortcode == 'stl_cyrillic') ? ['script' => 'cir'] : ['script' => 'lat'];

        return $this->legacy($atts,$content);

    }

    public function shortcode_callback($atts, ?string $content)
    {

        shortcode_atts([
            'script' => ''
        ], $atts);

        return ($atts['script'] == get_script()) ? $content : '';

        return $content;

    }

}
