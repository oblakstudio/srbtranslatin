<?php

namespace STL\Install\Interfaces;

use STL\Install\Data\Package;

interface Manages_Schema {
    /**
     * Sets up the database tables which the plugin needs to function.
     * WARNING: If you're fucking around with this method, make sure that it's safe to call regardless of the state of the database.
     *
     * This is called from install method above and runs only when installing or updating the plugin.
     *
     * @param  Package $package Package data.
     * @return static
     */
    public function create( Package $package ): static;

    /**
     * Verifies the table schema.
     *
     * @param  Package $package Package data.
     * @param  bool    $execute Should we execute the schema creation query.
     * @param  bool    $notify  Should we notify about the result.
     * @return array<string>
     */
    public function verify( Package $package, bool $execute = false, bool $notify = true ): array;
}
