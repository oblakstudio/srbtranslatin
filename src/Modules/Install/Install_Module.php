<?php
/**
 * Install_Module class file.
 *
 * @package SrbTransLatin
 */

namespace STL\Install;

use Automattic\Jetpack\Constants;
use DI\Container;
use STL\Common\Updates\Update_1;
use STL\Common\Updates\Update_2;
use STL\Install\Data\Package;
use wpdb;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Module;

/**
 * Install module definition.
 *
 * @since 1.0.0
 */
#[Module(
    container: 'stl',
    hook: 'plugins_loaded',
    priority: 999,
    handlers: array(
        Handlers\Install_Handler::class,
        Handlers\Migration_Handler::class,
        Handlers\Update_Handler::class,
    ),
)]
class Install_Module {
    public static function can_initialize(): bool {
        return \is_blog_installed();
    }

    /**
     *
     * Get the module configuration.
     *
     * @return array<string,mixed>
     */
    public static function configure(): array {
        return array(
            'install.pkg'                    => array(
                'name'    => \DI\factory(
                    static fn() => \__( 'SrbTransLatin - Serbian Latinisation', 'srbtranslatin' ),
                ),
                'schema'  => array(
                    'CREATE TABLE {{prefix}}aaa (
                        id bigint(20) unsigned not null auto_increment,
                        name varchar(191) not null,
                        slug varchar(191) not null,
                        primary key  (id),
                        unique key idx_name (name)
                    ) {{collate}};',
                ),
                'slug'    => \DI\get( 'install.slug' ),
                'updates' => array(
                    '0.0.1' => array( Update_1::class ),
                    '0.0.2' => array( Update_2::class ),
                ),
                'version' => array(
                    'core'   => \STL_VER,
                    'db'     => \STL_VER,
                    'schema' => \STL_VER,
                ),
            ),
            'install.slug'                   => 'srbtranslatin',
            Data\Package::class              => \DI\autowire()
                ->constructor( args: \DI\get( 'install.pkg' ), ),
            Interfaces\Manages_Schema::class => \DI\autowire( Services\Migrator::class ),
            wpdb::class                      => \DI\factory( static fn() => $GLOBALS['wpdb'] ),
        );
    }

    public function __construct( private Container $ctr ) {
    }

    #[Action( tag: 'init', priority: 10, args: 0 )]
    public function initialize(): void {
        $pkg = $this->ctr->get( Data\Package::class );

        if ( Constants::is_true( 'IFRAME_REQUEST' ) || ! $pkg->needs_core_update() ) {
            return;
        }

        /**
         * Runs the installation process for the given package.
         *
         * @param Package $pkg The package to install.
         * @since 1.0.0
         */
        \do_action( "{$pkg->get_slug()}_run_installer", $pkg );
    }
}
