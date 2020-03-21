<?php

namespace SGI\STL\Frontend\Utils;

use const SGI\STL\{
    DOMAIN,
    PATH
};

use function SGI\STL\Core\Utils\{
    get_stl_config,
    get_script
};

function get_current_url()
{

    global $wp;

    return home_url(
        add_query_arg(
            [],
            $wp->request
        )
    );

}

function script_selector(
    $args = [],
    $base_url = null,
    $depr_cyr_caption = null,
    $depr_lat_caption = null,
    $depr_inactive = null,
    $depr_wpml = null
) {

    if (!is_array($args)) :

        $f_args = [
            'selector_type' => $args,
            'separator'     => $base_url,
            'cir_caption'   => $depr_cyr_caption,
            'lat_caption'   => $depr_lat_caption,
            'inactive_only' => $depr_inactive
        ];

        $base_url = get_current_url();
        $args     = $f_args;

        unset($f_args);

        trigger_error(
            __(
                sprintf('You are using an old way calling the function %s, please read the documentation to see new function parameters', __FUNCTION__),
                DOMAIN
            ),
            E_USER_DEPRECATED
        );

    endif;

    $base_url = (empty($base_url)) ? get_current_url() : $base_url;
    $config   = get_stl_config();

    $args = wp_parse_args($args, [
        'selector_type' => 'oneline',
        'separator'     => '&nbsp;|&nbsp;',
        'cir_caption'   => 'Ћирилица',
        'lat_caption'   => 'Latinica',
        'inactive_only' => false,
        'active_script' => get_script(),
        'cir_link'      => add_query_arg($config['core']['param'], 'cir', $base_url),
        'lat_link'      => add_query_arg($config['core']['param'], 'lat', $base_url)
    ]);

    $html = '<div class="stl-selector">';

    switch ($args['selector_type']) :

        case 'links' :
            $html .= get_selector_template('list', $args);
            break;

        case 'list' :
        case 'dropdown' :
            $html .= get_selector_template('select', $args);
            break;

        case 'oneline' :
        default :
            $html .= get_selector_template('oneline', $args);
            break;



    endswitch;

    $html .= '</div>';

    echo $html;

}

function get_selector_template (string $template, array $args)
{

    $template_file = PATH."templates/selector/{$template}.php";

    /**
     * Filters the Template file for the selector, if you want to roll your own
     *
     * @since 2.0.0
     *
     * @param template_file - Template file for the oneline selector
     */
    $template_file = apply_filters("sgi/stl/selector/template/{$template}", $template_file);

    ob_start();

    include $template_file;

    return ob_get_clean();

}