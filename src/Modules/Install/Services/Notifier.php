<?php

namespace STL\Install\Services;

use STL\Install\Data\Package;

class Notifier {
    /**
     * Add a notice about schema creation failure.
     *
     * @param  Package       $pkg   Package data.
     * @param  array<string> $result Schema creation result.
     */
    public function add_schema_notice( Package $pkg, array $result ): void {
        \xwp_get_notice( "{$pkg->get_hook('missing', 'tables')}" )
            ->set_defaults()
            ->set_props(
                array(
                    'caps'        => 'manage_options',
                    'classes'     => 'alt',
                    'dismissible' => false,
                    'message'     => \sprintf(
                        '<strong>%s - %s</strong><br>%s',
                        \esc_html( $pkg->get_name() ),
                        \esc_html__( 'Schema creation failed', 'srbtranslatin' ),
                        \wp_kses_post( \implode( '<br>', $result ) ),
                    ),
                    'persistent'  => true,
                    'type'        => 'error',
                ),
            )->save( true );
    }

    public function remove_schema_notice( Package $pkg ): static {
        \xwp_get_notice( "{$pkg->get_hook('missing', 'tables')}" )?->delete( true );

        return $this;
    }

    public function clear_notices( Package $pkg ): static {
        $this->remove_schema_notice( $pkg );
        \xwp_get_notice( "{$pkg->get_hook('update', 'pending')}" )?->delete( true );

        return $this;
    }
}
