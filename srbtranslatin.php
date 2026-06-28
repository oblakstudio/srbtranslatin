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

// phpcs:disable WordPress.WhiteSpace.OperatorSpacing.SpacingBefore
defined( 'STL_FILE' ) || define( 'STL_FILE', __FILE__ );
defined( 'STL_ABS' )  || define( 'STL_ABS', dirname( STL_FILE ) . '/' );
defined( 'STL_BASE' ) || define( 'STL_BASE', plugin_basename( STL_FILE ) );
defined( 'STL_PATH' ) || define( 'STL_PATH', plugin_dir_path( STL_FILE ) );
defined( 'STL_VER' )  || define( 'STL_VER', '0.0.2' );
// phpcs:enable WordPress.WhiteSpace.OperatorSpacing.SpacingBefore

require_once __DIR__ . '/lib/Utils/core.php';
require __DIR__ . '/vendor/autoload_packages.php';

xwp_load_app(
    app: array(
        'attributes' => true,
        'autowiring' => true,
        'cache'      => false,
        'compile'    => false,
        'id'         => 'stl',
        'module'     => STL\App::class,
    ),
    priority: 10,
);

// require_once __DIR__ . '/lib/Utils/compat.php';
// require_once __DIR__ . '/lib/Utils/compat-sgi.php';
// require_once __DIR__ . '/vendor/autoload.php';
