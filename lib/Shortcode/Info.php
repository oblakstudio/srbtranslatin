<?php

namespace SGI\STL\Shortcode;

use function SGI\STL\Utils\{
    get_script,
    get_script_param
};

class Info
{

    public function __construct()
    {

       add_shortcode('stl_info', [&$this, 'shortcodeCallback']);

       

    }

    public function shortcodeCallback($atts, $content)
    {

        shortcode_atts([
            'type' => ''
        ], $atts);

        switch ($atts['type']) :

            case 'current_script' :
                return get_script();
                break;

            case 'script_identificator' :
                return get_script_param();
                break;

            case 'cyrillic_id' :
                return 'cir';
                break;

            case 'latin_id' :
                return 'lat';
                break;

        endswitch;

        return $atts['type'];

    }

}
