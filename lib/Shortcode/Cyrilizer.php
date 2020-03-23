<?php

namespace SGI\STL\Shortcode;

use function SGI\STL\Core\Utils\{
    get_script,
    is_serbian
};

class Cyrilizer implements Shortcode
{

    use Transliterable,
        Legacy;

    public function __construct()
    {

        $this->name = 'lang';

        add_shortcode('stl_cyr', [&$this, 'shortcode_callback']);
        add_shortcode('srlat_cyr', [&$this, 'legacy_callback']);
        add_shortcode('lang', [&$this, 'legacy_callback']);

    }

    public function shortcode_callback($atts, $content)
    {

        $script = get_script();

        if ($script == 'cir' || !is_serbian())
            return $content;

        $uuid = uniqid();

        self::$shortcodes[$uuid] = $content;

        return $uuid;

    }

}