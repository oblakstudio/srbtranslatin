<?php

namespace SGI\STL\Shortcode;

use const \SGI\STL\DOMAIN;

trait Legacy
{

    private $name;

    public function legacy_callback($atts, ?string $content)
    {

        trigger_error(
            __(
                sprintf('Shortcode %s will be deprecated in version 2.2, please read the documentation to see which shortcode to use',$this->name),
                DOMAIN
            ),
            E_USER_DEPRECATED
        );

        return $this->shortcode_callback($atts, $content);

    }

}