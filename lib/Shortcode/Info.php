<?php

namespace SGI\STL\Shortcode;

// use function SGI\STL\Core\Compat\{
//     stl_get_script_identifier,
//     stl_get_current_script,
//     stl_get_cyrillic_id,
//     stl_get_latin_id
// };

use function SGI\STL\Core\Utils\{
    get_script,
    get_script_param
};

class Info implements Shortcode, Multicode
{

    use Legacy;


    public function __construct()
    {

       $this->name = 'stl_get_*';

       add_shortcode('stl_info', [&$this, 'shortcode_callback']);

       add_shortcode('stl_get_current_script', [&$this, 'multicode_callback']);
       add_shortcode('stl_get_script_identificator', [&$this, 'multicode_callback']);
       add_shortcode('stl_get_cyrillic_id', [&$this, 'multicode_callback']);
       add_shortcode('stl_get_latin_id', [&$this, 'multicode_callback']);

    }

    public function multicode_callback($atts, ?string $content, ?string $shortcode)
    {

        switch ($shortcode) :

            case 'stl_get_current_script' :

                return stl_get_current_script();
                break;

            case 'stl_get_script_identificator' :

                return stl_get_script_identifier(); 
                break;

            case 'stl_get_cyrillic_id' :

                return stl_get_cyrillic_id();
                break;

            case 'stl_get_latin_id' :

                return stl_get_latin_id();
                break;

            default :
                return null;
                break;

        endswitch;

    }

    public function shortcode_callback($atts, ?string $content)
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
