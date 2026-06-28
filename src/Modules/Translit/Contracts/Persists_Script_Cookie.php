<?php
/**
 * Persists_Script_Cookie interface file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Contracts;

/**
 * Contract for persisting the selected script cookie.
 */
interface Persists_Script_Cookie {
    /**
     * Persist the selected script value.
     *
     * @param string $script Selected script.
     * @return void
     */
    public function persist(string $script): void;
}
