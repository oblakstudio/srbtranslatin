<?php

namespace SGI\STL\Admin\Utils;

use const SGI\STL\{
    VERSION,
    PATH
};

function get_stl_version()
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

function get_stl_settings()
{

    return [
        'stl_general'  => '',
        'stl_advanced' => '',
        'stl_media'    => '',
        'stl_ml'       => 'Multilanguage'

    ];

}