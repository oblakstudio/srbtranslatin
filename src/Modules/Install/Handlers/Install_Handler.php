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
class Install_Handler {
    public function __construct(
        private Installer $installer,
    ) {
    }

    /**
     * Run the updater.
     *
     * @param  Package $pkg Package data.
     */
    #[Action(
        '{%s}_run_installer',
        priority: 10,
        modifiers: array( 'install.slug' ),
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    public function run_installation( Package $pkg ): void {
        $this->installer->install( $pkg );

        /**
         * Runs after the installation is complete.
         *
         * @param Installer $installer The installer instance.
         * @since 1.0.0
         */
        \do_action( "{$pkg->get_slug()}_installed", $this->installer );
    }
}
