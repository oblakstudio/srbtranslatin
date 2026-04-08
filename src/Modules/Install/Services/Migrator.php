<?php //phpcs:disable Squiz.Commenting.FunctionComment.Missing

namespace STL\Install\Services;

use STL\Install\Data\Package;
use STL\Install\Interfaces\Manages_Schema;
use wpdb;

/**
 * Handles schema creation and verification.
 */
class Migrator implements Manages_Schema {
    /**
     * Constructor.
     *
     * @param Notifier $ntfr    Notifier service.
     * @param wpdb     $wpdb    WordPress database object.
     */
    public function __construct(
        protected Notifier $ntfr,
        protected wpdb $wpdb,
    ) {
    }

    public function create( Package $package ): static {
        $this->run( $package->get_schema(), true );

        return $this;
    }

    public function verify( Package $package, bool $execute = false, bool $notify = true ): array {
        $result = $execute
            ? $this->create( $package )->run( $package->get_schema(), true )
            : $this->run( $package->get_schema() );
        $result = $this->parse_dbdelta_output( $result );

        if ( array() === $result ) {
            if ( $notify ) {
                $this->remove_notice( $package );
            }

            $package->update_schema_version();

        } elseif ( $notify ) {
                $this->add_notice( $package, $result );
        }

        return $result;
    }

    /**
     * Adds a notice about schema issues.
     *
     * @param  Package       $pkg    Package data.
     * @param  array<string> $result Schema verification results.
     * @return static
     */
    public function add_notice( Package $pkg, array $result ): static {
        $this->ntfr->add_schema_notice( $pkg, $result );

        return $this;
    }

    public function remove_notice( Package $pkg ): static {
        $this->ntfr->remove_schema_notice( $pkg );

        return $this;
    }

    /**
     * Run the schema query.
     *
     * @param  array<string> $schema  Schema queries.
     * @param  bool          $execute Should we execute the query or only get the result.
     * @return array<string>
     */
    private function run( array $schema, bool $execute = false ): array {
        if ( ! \function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }
        $this->wpdb->hide_errors();

        $result = \dbDelta( $schema, $execute );

        $this->wpdb->show_errors();

        return $result;
    }

    /**
     * Parse the dbDelta output
     *
     * @param  array<int|string,string> $dbdelta dbDelta output.
     * @return array<string>
     */
    private function parse_dbdelta_output( array $dbdelta ): array {
        $output = array();

        foreach ( $dbdelta as $table => $result ) {
            if ( \preg_match( '/^Added index (\w+) (.*)$/i', $result, $matches ) ) {
                $index  = \str_replace( '.', ':', $matches[2] );
                $result = "Added index {$index}";
                $table  = "{$matches[1]}.{$index}";
            } elseif ( \is_int( $table ) ) {
                continue;
            }

            $output[] = $this->parse_dbdelta_row( $table, $result );
        }

        return $output;
    }

        /**
         * Parses the dbDelta row
         *
         * @param  string $target Target table or column.
         * @param  string $result dbDelta result.
         * @return string
         */
    private function parse_dbdelta_row( string $target, string $result ): string {
        $table  = \str_contains( $target, '.' ) ? \strtok( $target, '.' ) : $target;
        $column = \str_contains( $target, '.' ) ? \explode( '.', $target )[1] : null;

		//phpcs:disable SlevomatCodingStandard.Functions.RequireMultiLineCall
        $error = match ( true ) {

            "Added index {$column}" === $result  => \__( 'Table {{tbl}} is missing {{col}}', 'srbtranslatin' ),
            "Created table {$table}" === $result => \__( 'Table {{tbl}} is missing', 'srbtranslatin' ),
            "Added column {$target}" === $result => \__( 'Table {{tbl}} is missing {{col}} column', 'srbtranslatin' ),
            $table && $column                    => \__( 'Table {{tbl}} has an error in {{col}} column', 'srbtranslatin' ),
            default                              => \__( 'Error in table {{tbl}}', 'srbtranslatin' ),
        };
        //phpcs:enable SlevomatCodingStandard.Functions.RequireMultiLineCall

        return \strtr(
            $error,
            array(

                '{{col}}' => \strtr(
                    $column ?? \__( 'unknown', 'srbtranslatin' ),
                    array(
                        'KEY'    => \__( 'index', 'srbtranslatin' ),
                        'UNIQUE' => \__( 'unique', 'srbtranslatin' ),
                    ),
                ),
                '{{tbl}}' => $table,
            ),
        );
    }
}
