<?php
/**
 * Plugin Name:       SrbTransLatin - Serbian Latinisation
 * Plugin URI:        https://oblak.studio/plugins/srbtranslatin/
 * Description:       SrbTransLatin - Serbian Latinisation plugin allows you to have a website in both cyrillic and latin scripts
 * Author:            Oblak Studio
 * Author URI:        https://oblak.studio
 * Version:           0.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Text Domain:       srbtranslatin
 *
 * @package SrbTransLatin
 */

defined( 'ABSPATH' ) || exit;

defined( 'STL_PLUGIN_FILE' ) || define( 'STL_PLUGIN_FILE', __FILE__ );
defined( 'STL_PLUGIN_VERSION' ) || define( 'STL_PLUGIN_VERSION', '0.0.0' );
defined( 'STL_PLUGIN_BASENAME' ) || define( 'STL_PLUGIN_BASENAME', plugin_basename( STL_PLUGIN_FILE ) );
defined( 'STL_PLUGIN_PATH' ) || define( 'STL_PLUGIN_PATH', plugin_dir_path( STL_PLUGIN_FILE ) );

require_once __DIR__ . '/lib/Utils/core.php';
require_once __DIR__ . '/lib/Utils/compat.php';
require_once __DIR__ . '/lib/Utils/compat-sgi.php';
require_once __DIR__ . '/vendor/autoload.php';

STL();
