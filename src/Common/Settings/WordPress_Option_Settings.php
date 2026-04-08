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
            $values = \get_option( Settings_Schema::OPTION_NAME, array() );
            $values = \is_array( $values ) ? $values : array();
        }

        parent::__construct( $values );
    }
}
