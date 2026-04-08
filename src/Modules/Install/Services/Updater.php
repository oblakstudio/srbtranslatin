<?php

namespace STL\Install\Services;

use DI\Attribute\Inject;
use Invoker\InvokerInterface;
use STL\Install\Data\Package;

/**
 * Runs the updates.
 *
 * @mixin Package
 */
class Updater {
    /**
     * Constructor.
     *
     * @param Package          $pkg  Package data.
     * @param InvokerInterface $inv  Invoker service.
     */
    public function __construct(
        protected Package $pkg,
        protected InvokerInterface $inv,
    ) {
    }

    /**
     * Magic method to proxy calls to Version_Manager.
     *
     * @param string       $name Method name.
     * @param array<mixed> $args Method arguments.
     * @return mixed
     */
    public function __call( string $name, array $args ): mixed {
        if ( \method_exists( $this->pkg, $name ) ) {
            return $this->pkg->{$name}( ...$args );
        }

        return null;
    }

    /**
     * Get pending update callbacks.
     *
     * @param  bool $flatten Optional. Whether to flatten the result. Default true.
     * @return ($flatten is true ? array<int,callable|class-string|string|array{0:class-string,1:string}> : array<string,array<int,callable|class-string|string|array{0: class-string, 1: string}>>)
     */
    public function get_pending_cbs( bool $flatten = true ): array {
        $current = $this->pkg->get_curr_version( 'db' );
        $pending = array();

        foreach ( $this->get_update_cbs() as $version => $callbacks ) {
            if ( ! \version_compare( $current, $version, '<' ) ) {
                continue;
            }

            $pending[ $version ] = $callbacks;
        }

        return $flatten ? \array_merge( ...\array_values( $pending ) ) : $pending;
    }

    /**
     * Schedule a callback to run.
     *
     * @param  callable|class-string|string|array{0: class-string, 1: string} $callback Callback to schedule.
     * @param  int|null                                                       $timestamp Optional. Timestamp to run the callback at. Default now.
     * @return void
     */
    public function schedule_cb( callable|string|array|object $callback, ?int $timestamp = 0 ) {
        \as_schedule_single_action(
            timestamp:$timestamp ?? \time(),
            hook: $this->get_key( 'run', 'update', 'cb' ),
            args: array( 'callback' => $callback ),
            group: "{$this->get_slug()}-updates",
        );
    }

    public function update(): void {
        $start_time = \time();

        foreach ( $this->get_pending_cbs() as $loop => $callback ) {
            $this->schedule_cb( $callback, $start_time + $loop );
        }

        // \delete_transient( "{$this->get_slug()}_installing" );
        // die;
    }

    /**
     * Run a callback.
     *
     * @param  callable|class-string|string|array{0: class-string, 1: string} $callback Callback to run.
     * @return mixed
     */
    public function run_cb( callable|string|array|object $callback ): mixed {
        return $this->inv->call( $callback );
    }

    /**
     * Determine if an update is needed.
     *
     * @param  string|null $version Current version.
     * @return bool
     */
    public function needs_update( ?string $version = null ): bool {
        $version ??= $this->get_curr_version( 'db' );
        $updates   = \array_keys( $this->get_update_cbs() );

        \ksort( $updates );

        return '' !== $version && \version_compare( $version, \end( $updates ), '<' );
    }

    /**
     * Update the database version.
     *
     * @param  string|null $version New version.
     * @return static
     */
    public function update_version( ?string $version = null ): static {
        $this->pkg->update_db_version( $version );

        return $this;
    }

    /**
     * Determine if automatic updates are enabled.
     *
     * @return bool
     */
    public function can_autoupdate(): bool {
        /**
         * Filter whether automatic updates are enabled.
         *
         * @param bool $enable Whether to enable automatic updates. Default true.
         * @since 1.0.0
         */
        return \apply_filters( "{$this->get_slug()}_enable_autoupdate", true );
    }

    protected function get_key( string ...$parts ): string {
        \array_unshift( $parts, $this->get_slug() );

        return \implode( '_', $parts );
    }
}
