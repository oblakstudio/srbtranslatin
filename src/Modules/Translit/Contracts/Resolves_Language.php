<?php
/**
 * Resolves_Language interface file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Contracts;

/**
 * Contract for resolving the chosen language.
 */
interface Resolves_Language {
    /**
     * Resolve the current language.
     *
     * @return string|null
     */
    public function resolve_language(): ?string;
}
