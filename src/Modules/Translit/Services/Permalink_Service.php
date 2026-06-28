<?php
/**
 * Permalink_Service class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Services;

use Oblak\Transliterator;

/**
 * Handle permalink transliteration parity.
 */
final class Permalink_Service {
    private const LOCALES_WITH_CORE_SLUG_SUPPORT = array( 'sr_RS', 'bs_BA' );

    /**
     * Constructor.
     *
     * @param bool $fix_permalinks Whether permalink transliteration is enabled.
     * @param callable|null $locale_getter Locale provider.
     * @param Transliterator|null $transliterator Transliterator instance.
     */
    public function __construct(
        private bool $fix_permalinks,
        private mixed $locale_getter = null,
        private ?Transliterator $transliterator = null,
    ) {
        $this->transliterator ??= Transliterator::instance();
    }

    /**
     * Transliterate slugs when enabled and not already handled by WordPress locale behavior.
     *
     * @param string $title Sanitized title.
     * @return string
     */
    public function sanitize_title( string $title ): string {
        if ( ! $this->fix_permalinks || $this->locale_handles_slugs() ) {
            return $title;
        }

        return $this->transliterator->cirToLat( $title );
    }

    /**
     * Determine whether the current locale should be left to WordPress core.
     *
     * @return bool
     */
    private function locale_handles_slugs(): bool {
        return \in_array( $this->get_locale(), self::LOCALES_WITH_CORE_SLUG_SUPPORT, true );
    }

    /**
     * Resolve the current locale.
     *
     * @return string
     */
    private function get_locale(): string {
        if ( \is_callable( $this->locale_getter ) ) {
            $locale = \call_user_func( $this->locale_getter );

            return \is_string( $locale ) && '' !== $locale ? $locale : \get_locale();
        }

        return \get_locale();
    }
}
