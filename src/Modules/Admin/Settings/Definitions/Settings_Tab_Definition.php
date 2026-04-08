<?php
/**
 * Settings tab definition contract.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Admin\Settings\Definitions;

interface Settings_Tab_Definition {
    /**
     * Build the tab schema.
     *
     * @return array{tab: array<string,string|null>, section: array<string,string|null>, fields: array<int,array<string,mixed>>}
     */
    public function build(): array;
}
