<?php
/**
 * Settings page config contract.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Core;

interface Settings_Page_Config {
    /**
     * @return array{
     *     page: array<string,string>,
     *     tabs: array<int,array<string,string|null>>,
     *     sections: array<int,array<string,string|null>>,
     *     fields: array<int,array<string,mixed>>
     * }
     */
    public function get(): array;
}
