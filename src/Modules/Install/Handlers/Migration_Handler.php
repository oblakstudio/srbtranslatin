<?php //phpcs:disable Squiz.Commenting.FunctionComment.MissingParamTag

namespace STL\Install\Handlers;

use STL\Install\Data\Package;
use STL\Install\Interfaces\Manages_Schema;
use WP_CLI;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Handler;

use function WP_CLI\Utils\get_flag_value;

#[Handler( container: 'stl', strategy: Handler::INIT_JUST_IN_TIME )]
class Migration_Handler {
    /**
     * Package data.
     *
     * @var Package
     */
    private Package $pkg;

    /**
     * Check if the handler can be initialized.
     *
     * @param  Package $package Package data.
     * @return bool
     */
    public static function can_initialize( Package $package ): bool {
        return $package->has_schema();
    }

    /**
     * Constructor.
     *
     * @param Manages_Schema $migrator Migrator service.
     */
    public function __construct( private Manages_Schema $migrator ) {
    }

    /**
     * Update the database version in the options table.
     *
        * @param  Package $package Package data.
     */
    #[Action(
        '{%s}_run_migrator',
        priority: 10,
        modifiers: array( 'install.slug' ),
        args: 1,
        invoke: Action::INV_PROXIED,
    )]
    public function create_schema( Package $package ): void {
        $this->migrator->create( $package )->verify( $package );
    }

    /**
     * Update the database version in the options table.
     *
        * @param  Package $package Package data.
     */
    #[Action( tag: 'cli_init', priority: 10, context: Action::CTX_CLI, args: 0, invoke: Action::INV_PROXIED )]
    public function register_command( Package $package ): void {
        WP_CLI::add_command( "{$package->get_slug()} verify_tables", array( $this, 'verify_tables_cmd' ) );

        $this->pkg = $package;
    }

    /**
     * Runs the DB verification routine and outputs the results to the CLI.
     *
     * ## OPTIONS
     *
     * [--create]
     * : Create the missing tables.
     *
     * @phpstan-ignore missingType.iterableValue, missingType.iterableValue
     */
    public function verify_tables_cmd( array $args, array $flags ): void {
        $result = $this->migrator->verify( $this->pkg );

        if ( array() === $result ) {
            WP_CLI::success( \__( 'Database schema is up to date.', 'srbtranslatin' ) );
            return;
        }

        WP_CLI::warning( \__( 'Database schema issues detected:', 'srbtranslatin' ) );

        foreach ( $result as $issue ) {
            WP_CLI::line( "- {$issue}" );
        }

        if ( ! get_flag_value( $flags, 'create', false ) ) {
            WP_CLI::line(
                \__(
                    'Run the command again with --create to create the missing tables.',
                    'srbtranslatin',
                ),
            );
            return;
        }

        WP_CLI::line( \__( 'Creating missing tables...', 'srbtranslatin' ) );

        array() === $this->migrator->verify( $this->pkg, true, true )
            ? WP_CLI::success( \__( 'Database schema is now up to date.', 'srbtranslatin' ) )
            : WP_CLI::error( \__( 'There were errors updating the database schema.', 'srbtranslatin' ) );

        exit;
    }
}
