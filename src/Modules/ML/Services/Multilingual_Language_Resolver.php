<?php
/**
 * Multilingual_Language_Resolver class file.
 *
 * @package SrbTransLatin
 * @subpackage ML
 */

namespace STL\ML\Services;

use STL\Translit\Contracts\Resolves_Language;

/**
 * Resolve the active locale from the first available multilingual resolver.
 */
final class Multilingual_Language_Resolver implements Resolves_Language {
    /**
     * @param array<int,Resolves_Language> $resolvers Ordered language resolvers.
     */
    public function __construct( private array $resolvers ) {
    }

    /**
     * Resolve the active locale.
     *
     * @return string|null
     */
    public function resolve_language(): ?string {
        foreach ( $this->resolvers as $resolver ) {
            $language = $resolver->resolve_language();

            if ( \is_string( $language ) && '' !== $language ) {
                return $language;
            }
        }

        return null;
    }
}
