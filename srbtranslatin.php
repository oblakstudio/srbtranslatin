<?php
/*
 * Plugin Name:       SrbTransLatin - Serbian Latinisation
 * Plugin URI:        https://sgi.io/plugins/srbtranslatin/
 * Description:       SrbTransLatin - Serbian Latinisation plugin allows you to have a website in both cyrillic and latin scripts
 * Author:            Sibin Grasic
 * Author URI:        https://sgi.io
 * Version:           2.0
 * Requires at least: 4.8
 * Requires PHP:      7.0
 * Text Domain:       SrbTransLatin
 */

namespace SGI\STL;
use \SGI\STL\Core\Bootstrap as STL;

// Prevent direct access
!defined('WPINC') && die;

!defined(__NAMESPACE__ . '\FILE')     && define(__NAMESPACE__ . '\FILE', __FILE__);                   // Define Main plugin file
!defined(__NAMESPACE__ . '\BASENAME') && define(__NAMESPACE__ . '\BASENAME', plugin_basename(FILE));  // Define Basename
!defined(__NAMESPACE__ . '\PATH')     && define(__NAMESPACE__ . '\PATH', plugin_dir_path( FILE ));    // Define internal path
!defined(__NAMESPACE__ . '\VERSION')  && define (__NAMESPACE__ . '\VERSION', '2.0');                // Define internal version
!defined(__NAMESPACE__ . '\DOMAIN')   && define (__NAMESPACE__ . '\DOMAIN', 'SrbTransLatin');         // Define Text domain

// Bootstrap the plugin
require (PATH . '/vendor/autoload.php');

// Run the plugin
function run_stl()
{

    global $wp_version;

    if (version_compare( PHP_VERSION, '7.0.0', '<' ))
        throw new \Exception(__('STL - Serbian Latinisation plugin requires PHP 7.0 or greater.', DOMAIN));

    if (version_compare($wp_version, '4.8', '<'))
        throw new \Exception(__('STL - Serbian Latinisation plugin requires WordPress 4.8.0.', DOMAIN));

    return new STL();

}

// And awaaaaay we goooo
try {

    run_stl();

} catch (\Exception $e) {


    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    deactivate_plugins( __FILE__ );
    wp_die($e->getMessage());

}