<?php

namespace SGI\STL\Shortcode;

use function SGI\STL\Utils\get_script;

class SelectiveOutput
{

    public function __construct()
    {

        add_shortcode('stl_show', [&$this, 'shortcode_callback']);

    }


    public function shortcode_callback($atts, $content)
    {

        shortcode_atts([
            'script' => ''
        ], $atts);

        return ($atts['script'] == get_script()) ? $content : '';

        return $content;

    }

}
