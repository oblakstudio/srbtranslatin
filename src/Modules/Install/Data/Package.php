<?php //phpcs:disable Squiz.Commenting.FunctionComment.MissingParamName, Squiz.Commenting.FunctionComment.MissingParamTag

namespace STL\Install\Data;

/**
 * Describes an installable package.
 */
class Package {
    /**
     * Package name.
     *
     * @var string
     */
    protected string $name;

    /**
     * Package slug.
     *
     * @var string
     */
    protected string $slug;
    /**
     * Available versions.
     *
     * @var array{
     *  core: string,
     *  db: string,
     *  schema: string
     * }
     */
    protected array $version = array(
        'core'   => '0.0.0',
        'db'     => '0.0.0',
        'schema' => '0.0.0',
    );

    /**
     * Current versions.
     *
     * @var array{
     *  core?: string,
     *  db?: string,
     *  schema?: string
     * }
     */
    protected array $curr_ver = array();

    /**
     * Table schema definitions.
     *
     * @var array<string>
     */
    protected array $schema = array();


    /**
     * Update callbacks
     *
     * @var array<string,array<int,callable|class-string|string|array{0: class-string, 1: string}>>
     */
    protected array $updates = array();

    /**
     * Constructor.
     *
     * @param array<string,mixed> $args Package arguments.
     */
    public function __construct( array $args ) {
        foreach ( $args as $key => $value ) {
            \is_callable( array( $this, "set_{$key}" ) )
                ? $this->{"set_{$key}"}( $value )
                : $this->set_prop( $key, $value );
        }
    }

    /**
     * Get the package ID.
     *
     * @return string
     */
    public function get_id(): string {
        $slug = \str_replace( '-', '_', $this->get_slug() );

        return \strtoupper( \preg_replace( '/[^a-zA-Z0-9_]/', '_', $slug ) );
    }

    /**
     * Get a hook name for the package.
     *
     * @param  string ...$parts Hook name parts.
     * @return string
     */
    public function get_hook( string ...$parts ): string {
        \array_unshift( $parts, $this->get_slug() );
        return \implode( '_', \array_unique( \array_filter( $parts ) ) );
    }

    /**
     * Get the package name.
     *
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Get the package slug.
     *
     * @return string
     */
    public function get_slug(): string {
        return $this->slug;
    }

    /**
     * Get the schema definitions.
     *
     * @return array<string>
     */
    public function get_schema(): array {
        return $this->schema;
    }

    /**
     * Get the available version.
     *
     * @param  'core'|'db'|'schema' $type Version type.
     * @return string
     */
    public function get_code_version( string $type = 'core' ): string {
        return $this->version[ $type ] ?? '0.0.0';
    }

    /**
     * Get the current version.
     *
     * @param  'core'|'db'|'schema' $type Version type.
     * @return string
     */
    public function get_curr_version( string $type = 'core' ): string {
        return $this->curr_ver[ $type ] ??= \get_option( $this->get_option( $type ), '' );
    }

    /**
     * Get all update callbacks.
     *
     * @return array<string,array<int,callable|class-string|string|array{0:class-string, 1:string}>>
     */
    public function get_update_cbs(): array {
        return $this->updates;
    }

    /**
     * Set the install time.
     *
     * @param  int|null $time Optional. Install time. Default null (current time).
     * @return static
     */
    public function set_install_time( ?int $time = null ): static {
        \add_option(
            option: "{$this->slug}_install_time",
            value: $time ?? \time(),
            autoload: false,
        );

        return $this;
    }

    /**
     * Update the core version.
     *
     * @param  string|null $version Optional. Version to set. Default null (code version).
     * @return static
     */
    public function update_core_version( ?string $version = null ): static {
        return $this->update_version( $version, 'core' );
    }

    /**
     * Update the database version.
     *
     * @param  string|null $version Optional. Version to set. Default null (code version).
     * @return static
     */
    public function update_db_version( ?string $version = null ): static {
        return $this->update_version( $version, 'db' );
    }

    /**
     * Update the schema version.
     *
     * @param  string|null $version Optional. Version to set. Default null (code version).
     * @return static
     */
    public function update_schema_version( ?string $version = null ): static {
        return $this->update_version( $version, 'schema' );
    }

    /**
     * Determine if there is an update available for the package core.
     *
     * @return bool
     */
    public function needs_core_update(): bool {
        return $this->needs_update( 'core' );
    }

    /**
     * Determine if there is a schema defined.
     *
     * @return bool
     */
    public function has_schema(): bool {
        return array() !== $this->schema;
    }

    /**
     * Determine if there are update callbacks defined.
     *
     * @return bool
     */
    public function has_update_cbs(): bool {
        return array() !== $this->updates;
    }

    /**
     * Determine if an update is needed.
     *
     * @param  'core'|'db'|'schema' $type Version type.
     * @return bool
     */
    public function needs_update( string $type = 'core' ): bool {
        return \version_compare(
            $this->get_curr_version( $type ),
            $this->get_code_version( $type ),
            '<',
        );
    }

    /**
     * Determine if we need a database update.
     *
     * @return bool
     */
    public function needs_db_update(): bool {
        $version = $this->get_curr_version( 'db' );
        $updates = \array_keys( $this->get_update_cbs() );

        \ksort( $updates );

        return '' !== $version && \version_compare( $version, \end( $updates ), '<' );
    }

    /**
     * Determine if the package is freshly installed.
     *
     * @return bool
     */
    public function is_fresh(): bool {
        return '' === $this->get_curr_version( 'core' );
    }

    /**
     * Update the stored version.
     *
     * @param  string|null          $version Optional. Version to set. Default null (code version).
     * @param  'core'|'db'|'schema' $type Version type.
     * @return static
     */
    protected function update_version( ?string $version = null, string $type = 'core' ): static {
        \update_option(
            option: $this->get_option( $type ),
            value: $version ?? $this->get_code_version( $type ),
            autoload: false,
        );
        return $this;
    }

    /**
     * Set the available versions.
     *
     * @param  array{
     *  core?: string,
     *  db?: string,
     *  schema?: string
     * } $version Versions array.
     * @return static
     */
    protected function set_version( array $version ): static {
        $this->version = \array_merge( $this->version, $version );

        return $this;
    }

    /**
     * Set the schema definitions.
     *
     * @param  array<string> $schema Schema definitions.
     * @return static
     */
    protected function set_schema( array $schema ): static {
        global $wpdb;

        $this->schema = \array_map(
            static fn( $sql ) => \strtr(
                $sql,
                array(
                    '{{collate}}' => $wpdb->get_charset_collate(),
                    '{{prefix}}'  => $wpdb->prefix,
                ),
            ),
            $schema,
        );

        return $this;
    }

    /**
     * Set a property.
     *
     * @param  string $prop  Property name.
     * @param  mixed  $value Property value.
     * @return static
     */
    protected function set_prop( string $prop, mixed $value ): static {
        $this->{$prop} = $value;

        return $this;
    }

    /**
     * Get a property.
     *
     * @param  string $prop Property name.
     * @return mixed
     */
    protected function get_prop( string $prop ): mixed {
        return \is_callable( array( $this, "get_{$prop}" ) ) ? $this->{"get_{$prop}"}() : $this->{$prop};
    }

    /**
     * Get the option name for storing version.
     *
     * @param  'core'|'db'|'schema' $type Version type.
     * @return string
     */
    private function get_option( string $type ): string {
        return 'core' === $type ? "{$this->slug}_version" : "{$this->slug}_{$type}_version";
    }
}
