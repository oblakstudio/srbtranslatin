<?php

namespace STL\Install\Handlers;

use Automattic\Jetpack\Constants;
use DI\Attribute\Inject;
use STL\Install\Data\Package;
use STL\Install\Services\Installer;
use STL\Install\Services\Updater;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Handler;

#[Handler( container: 'stl', strategy: Handler::INIT_JUST_IN_TIME )]
class Update_Handler {
    /**
     * Check if the handler can be initialized.
     *
     * @param  Updater $updater Updater service.
     * @return bool
     */
    public static function can_initialize( Updater $updater ): bool {
        return ! Constants::is_defined( 'IFRAME_REQUEST' ) && $updater->needs_update();
    }

    /**
     * Constructor.
     *
     * @param Updater $updater Updater service.
     */
    public function __construct(
        protected Updater $updater,
    ) {
    }

    /**
     * Run the updater.
     *
     * @param  Package $package Package data.
     */
    #[Action(
        '{%s}_run_db_updater',
        priority: 10,
        modifiers: array( 'install.slug' ),
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    public function run_updater( Package $package ): void {
        // if (!$this->updater->)
    }


    /**
     * Run a single update callback.
     *
     * @param  callable|class-string|string|array{0: class-string, 1: string} $callback Callback to schedule.
     * @return void
     */
    #[Action(
        '{%s}_run_update_cb',
        priority: 10,
        modifiers: array( 'install.slug' ),
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    public function run_update_callback( string|callable|array|object $callback ): void {
        try {
            $res = $this->updater->run_cb( $callback ) ?? true;

        } catch ( \Throwable ) {
            $res = false;
        } finally {
            if ( true !== $res ) {
                $this->updater->schedule_cb( $callback );
            }
        }
        \error_log( \print_r( $callback, true ) );
    }

    /**
     * Update the database version in the options table.
     *
     * @param ?string $version Optional. Version to set. If null, sets to the latest version.
     */
    #[Action(
        '{%s}_update_db_version',
        priority: 10,
        modifiers: array( 'install.slug' ),
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    public function update_db_version( ?string $version = null ): void {
        $this->updater->update_version( $version );
    }
}
