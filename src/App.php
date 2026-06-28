<?php
/**
 * App class file.
 *
 * @package SrbTransLatin
 */

namespace STL;

use Psr\Container\ContainerInterface;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Module;

/**
 * Plugin module
 */
#[Module(
    container: 'stl',
    hook: 'plugins_loaded',
    priority: 50,
    imports: array(
        Core\Core_Module::class,
        // Admin\Admin_Module::class,
        Install\Install_Module::class,
        ML\ML_Module::class,
        Translit\Translit_Module::class,
        Support\Support_Module::class,
    ),
    handlers: array(),
)]
class App {
    /**
     * Get the module configuration.
     *
     * @return array<string,mixed>
     */
    public static function configure(): array {
        return array(
            'xwp.inv.tag' => \DI\factory(
                static fn( string $tag, ContainerInterface $ctr ) =>
                        \DI\string( $tag )->resolve( $ctr ),
            ),
        );
    }

    /**
     * Fires the `srbtranslatin_loaded` action after all modules have been loaded.
     *
     * @return void
     */
    #[Action( tag: 'plugins_loaded', priority: 100 )]
    public function on_plugins_loaded(): void {
        /**
         * Load the plugin.
         *
         * This action is used to load the plugin after all modules have been loaded.
         *
         * @since 4.0.0
         */
        \do_action( 'srbtranslatin_loaded' );
    }

    #[Action( tag: 'init', priority: 0 )]
    public function on_init(): void {
        $this->load_textdomain();

        /**
         * Action triggered before SrbTransLatin initialization.
         *
         * @since 4.0.0
         */
        \do_action( 'before_srbtranslatin_init' );

        /**
         * Action triggered after SrbTransLatin initialization.
         *
         * @since 4.0.0
         */
        \do_action( 'srbtranslatin_init' );
    }

    /**
     * Load the plugin textdomain.
     *
     * @return void
     */
    private function load_textdomain(): void {
        \load_plugin_textdomain( 'srbtranslatin', false, \dirname( \STL_BASE ) . '/languages' );
    }
}
