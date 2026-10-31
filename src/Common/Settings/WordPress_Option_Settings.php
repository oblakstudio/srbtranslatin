<?php
/**
 * WordPress option-backed plugin settings.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Common\Settings;

final class WordPress_Option_Settings extends Array_Settings {
    /**
     * @param array<string,mixed>|null $values Optional seed values for tests.
     */
    public function __construct( ?array $values = null ) {
        if ( null === $values ) {
            $values = self::saved_values();
        }

        parent::__construct( $values );
    }

    /**
     * Read existing 3.x options until settings are saved in the current format.
     *
     * @return array<string,mixed>
     */
    public static function saved_values( string $option_name = Settings_Schema::OPTION_NAME ): array {
        $values = \get_option( $option_name, null );
        if ( \is_array( $values ) ) {
            return $values;
        }
        $values = array();
        if ( Settings_Schema::OPTION_NAME !== $option_name ) {
            return $values;
        }
        foreach ( Settings_Schema::defaults() as $group => $defaults ) {
            $legacy = \get_option( 'srbtranslatin_' . $group, array() );
            if ( ! \is_array( $legacy ) ) {
                continue;
            }
            foreach ( $defaults as $key => $default ) {
                if ( \array_key_exists( $key, $legacy ) ) {
                    $raw = $legacy[ $key ];
                    $values[ $key ] = \is_bool( $default ) && \in_array( $raw, array( 'yes', 'no' ), true ) ? 'yes' === $raw : $raw;
                }
            }
        }
        return $values;
    }
}
