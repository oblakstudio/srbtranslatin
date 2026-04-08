<?php

namespace STL\Install\Services;

use DI\Attribute\Inject;
use STL\Install\Data\Package;
use STL\Install\Interfaces\Manages_Schema;

/**
 * Installer class
 *
 * @mixin Package
 */
class Installer {
    protected string $id;

    public function __construct(
        protected Notifier $notifier,
    ) {
    }

    public function can_install( Package $pkg ): bool {
        return \is_blog_installed() && ! $this->is_installing( $pkg );
    }

    public function is_installing( Package $pkg ): bool {
        return 'yes' === \get_transient( "{$pkg->get_slug()}_installing" );
    }

    public function install( Package $pkg ): void {
        if ( ! $this->can_install( $pkg ) ) {
            return;
        }

        \set_transient( $pkg->get_hook( 'installing' ), 'yes', \MINUTE_IN_SECONDS * 10 );
        \defined( "{$pkg->get_id()}_INSTALLING" ) || \define( "{$pkg->get_id()}_INSTALLING", true );

        try {

            // $this->update_db_version();

            $this->install_core( $pkg );
        } finally {
            \delete_transient( $pkg->get_hook( 'installing' ) );
            // die;
        }

        // $this->ver->set_install_time();
    }

    protected function install_core( Package $pkg ): void {
        if ( $pkg->is_fresh() && ! \get_option( $pkg->get_hook( 'newly', 'installed' ) ) ) {
            \update_option( $pkg->get_hook( 'newly', 'installed' ), 'yes' );
        }

        $this->clear_notices( $pkg );
        $this->create_schema( $pkg );
        $this->create_options();
        $this->migrate_options();
        $this->create_roles();
        $this->setup_environment();
        $this->create_terms();
        $this->create_cron_jobs();
        $this->create_files();
        $this->setup_activation();
        $this->update_core_version( $pkg );
        // $this->update_db_version();

        // Core installation logic goes here.
    }

    protected function clear_notices( Package $pkg ): void {
        $this->notifier->clear_notices( $pkg );
    }

    protected function get_key( string ...$parts ): string {
        \array_unshift( $parts, $this->get_slug() );

        return \implode( '_', $parts );
    }

    /**
     * Creates the database schema
     *
     * @param Package $pkg The package instance.
     */
    protected function create_schema( Package $pkg ): void {
        if ( ! $pkg->has_schema() ) {
            return;
        }

        /**
         * Performs the schema creation.
         *
         * @param Package $package The package instance.
         * @since 1.0.0
         */
        \do_action( $pkg->get_hook( 'run', 'migrator' ), $pkg );
    }

    protected function create_options(): void {
        // Noop.
    }

    protected function migrate_options(): void {
        // Noop.
    }

    protected function create_roles(): void {
        // Noop.
    }

    protected function setup_environment(): void {
        // Noop.
    }

    /**
     * Create terms needed for the plugin
     *
     * This method should be overridden by the child class
     */
    protected function create_terms(): void {
        // Noop.
    }

    /**
     * Create cron jobs needed for the plugin
     *
     * This method should be overridden by the child class
     */
    protected function create_cron_jobs(): void {
        // Noop.
    }

    /**
     * Create files needed for the plugin
     *
     * This method should be overridden by the child class
     */
    protected function create_files(): void {
        // Noop.
    }

    /**
     * Set the activation transient
     */
    protected function setup_activation(): void {
        // Noop.
    }

    protected function update_core_version( Package $package ): void {
        $package->update_core_version();
    }

    // protected function update_db_version(): void {
    // if ( ! $this->pkg->has_update_cbs() ) {
    // return;
    // }

    // **
    // * Runs the update callbacks
    // *
    // * @since 1.0.0
    // * @param Package $package The package instance.
    // */
    // \do_action( $this->get_key( 'run', 'db', 'updater' ), $this->pkg );

    // if ( ! $this->upd->needs_update() ) {
    // $this->upd->update_version();
    // return;
    // }

    // do_action($this->get_key("needs","db","update"), $this->get_updates() );

    // if ( ! $this->upd->can_autoupdate() ) {
    // return;
    // }

    // $this->upd->update();
    // }
}
