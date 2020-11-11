<?php

namespace SGI\STL\Shortcode;

use function SGI\STL\Utils\{
    get_script,
    is_serbian
};

/**
 * Legacy shortcodes to be removed in the next version of the plugin
 * 
 * @package SGI\STL\Shortcode
 * @deprecated 2.5
 * @since 2.4
 */
class LegacyShortcodes
{

    use Transliterable;

    public function __construct()
    {

        add_shortcode('srlat_cyr', [&$this, 'transliterateCallback']);
        add_shortcode('lang', [&$this, 'transliterateCallback']);

        add_shortcode('stl_is_cyrillic', [&$this, 'whichScriptCallback']);
        add_shortcode('stl_is_latin', [&$this, 'whichScriptCallback']);

        add_shortcode('stl_get_current_script', [&$this, 'infoCallback']);
        add_shortcode('stl_get_script_identificator', [&$this, 'infoCallback']);
        add_shortcode('stl_get_cyrillic_id', [&$this, 'infoCallback']);
        add_shortcode('stl_get_latin_id', [&$this, 'infoCallback']);

        add_shortcode('stl_replace',  [&$this, 'replaceCallback']);

    }

    public function throwError($shortcode_name)
    {

        trigger_error(
            __(sprintf('Shortcode %s will be deprecated in version 2.5, please read the documentation to see which shortcode to use', $shortcode_name), 'SrbTransLatin'),
            E_USER_DEPRECATED
        );

    }

    public function transliterateCallback($atts, $content, $shortcode)
    {

        $this->throwError($shortcode);

        $script = get_script();

        if ($script == 'cir' || !is_serbian())
            return $content;

        $uuid = uniqid();

        self::$shortcodes[$uuid] = $content;

        return $uuid;

    }

    public function whichScriptCallback($atts, $content, $shortcode)
    {

        $this->throwError($shortcode);

        $script = ($shortcode == 'stl_is_cyrillic') ? ['script' => 'cir'] : ['script' => 'lat'];

        return do_shortcode("[stl_show script='{$script}']{$content}[/stl_show]");

    }

    public function infoCallback($atts, $content, $shortcode)
    {

        $this->throwError($shortcode);

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

    public function replaceCallback($atts, $content, $shortcode)
    {

        $this->throwError($shortcode);

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