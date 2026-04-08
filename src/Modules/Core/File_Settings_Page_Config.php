<?php
/**
 * File-backed settings page config provider.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Core;

final class File_Settings_Page_Config implements Settings_Page_Config {
    public function get(): array {
        /** @var array{
         *     page: array<string,string>,
         *     tabs: array<int,array<string,string|null>>,
         *     sections: array<int,array<string,string|null>>,
         *     fields: array<int,array<string,mixed>>
         * } $config
         */
        $config = require STL_PATH . 'config/settings.php';

        return $config;
    }
}
