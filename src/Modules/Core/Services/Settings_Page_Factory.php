<?php

namespace STL\Core\Services;

class Settings_Page_Factory {
    /**
     * Makes a Settings_Page instance from the given configuration array.
     *
     * @param array<string,mixed> $config Settings page configuration array.
     * @return Settings_Page
     */
    public static function make( array $config ): Settings_Page {
        return new Settings_Page( $config );
    }
}
