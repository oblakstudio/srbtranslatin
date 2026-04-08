<?php
/**
 * Array-backed plugin settings.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Common\Settings;

class Array_Settings implements Plugin_Settings {
    /**
     * @param array<string,mixed>              $values   Saved flat option values.
     * @param array<string,array<string,mixed>> $defaults Grouped default values.
     */
    public function __construct(
        private array $values,
        private array $defaults = array(),
    ) {
        if ( array() === $this->defaults ) {
            $this->defaults = Settings_Schema::defaults();
        }
    }

    public function get( string $group, string $key, mixed $fallback = null ): mixed {
        if ( array_key_exists( $key, $this->values ) ) {
            return $this->values[ $key ];
        }

        if ( isset( $this->defaults[ $group ] ) && array_key_exists( $key, $this->defaults[ $group ] ) ) {
            return $this->defaults[ $group ][ $key ];
        }

        return $fallback;
    }
}
