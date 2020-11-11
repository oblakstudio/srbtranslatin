<?php

namespace SGI\STL\Utils;

use const SGI\STL\VERSION;
use const SGI\STL\PATH;

/**
 * Return current version of the plugin
 * 
 * @return string SrbTransLatin verstion
 * 
 * @since 2.0
 */
function getVersion()
{

    $installed_ver = get_option('sgi/stl/ver');

    if (!$installed_ver) :

        $installed_ver = VERSION;

    endif;

    return $installed_ver;

}

function get_settings_template(string $section)
{

    $template_file = PATH."templates/settings/{$section}.php";

    include $template_file;    

}
