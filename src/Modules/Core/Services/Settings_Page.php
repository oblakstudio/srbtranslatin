<?php
/**
 * Plugin settings rendered and saved through the WordPress Settings API.
 *
 * @package SrbTransLatin
 */

declare(strict_types=1);

namespace STL\Core\Services;

use STL\Common\Settings\WordPress_Option_Settings;

final class Settings_Page {
    /** @param array<string,mixed> $config Settings page schema. */
    public function __construct( private array $config ) {
    }

    public function init(): void {
        \add_action( 'admin_menu', array( $this, 'register_menu' ) );
        \add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function register_menu(): void {
        $page = $this->config['page'];
        \add_submenu_page(
            $page['parent_slug'],
            $page['page_title'],
            $page['menu_title'],
            $page['capability'],
            $page['slug'],
            $this->render( ... ),
        );
    }

    public function register_settings(): void {
        $page = $this->config['page'];
        \register_setting( $page['slug'], $page['option_name'], array(
            'type'              => 'array',
            'sanitize_callback' => $this->sanitize( ... ),
            'default'           => array(),
        ) );
    }

    /**
     * Preserve fields from other tabs and disabled legacy options.
     *
     * @param mixed $input Submitted option values.
     * @return array<string,mixed>
     */
    public function sanitize( mixed $input ): array {
        $values = $this->saved_values();
        if ( ! \is_array( $input ) ) {
            $this->invalid_value();
            return $values;
        }

        foreach ( $this->config['fields'] as $field ) {
            $id = $field['id'];
            if ( 'description' === $field['type'] || ! \array_key_exists( $id, $input ) || $this->is_disabled( $field ) ) {
                continue;
            }
            $raw = $input[ $id ];
            if ( ! \is_scalar( $raw ) ) {
                $this->invalid_value();
                continue;
            }
            if ( 'checkbox' === $field['type'] ) {
                if ( ! \in_array( $raw, array( '0', '1', 0, 1, false, true ), true ) ) {
                    $this->invalid_value();
                    continue;
                }
                $values[ $id ] = \in_array( $raw, array( '1', 1, true ), true );
                continue;
            }
            $value   = \sanitize_text_field( (string) $raw );
            $options = $field['extras']['options'] ?? null;
            if ( \is_array( $options ) && ! \array_key_exists( $value, $options ) ) {
                $this->invalid_value();
                continue;
            }
            if ( 'url_param' === $id && 1 !== \preg_match( '/^[A-Za-z][A-Za-z0-9_-]*$/D', $value ) ) {
                $this->invalid_value();
                continue;
            }
            $values[ $id ] = $value;
        }
        return $values;
    }

    public function render(): void {
        $page = $this->config['page'];
        if ( ! \current_user_can( $page['capability'] ) ) {
            \wp_die( \esc_html__( 'You do not have permission to manage these settings.', 'srbtranslatin' ) );
        }
        \wp_enqueue_script( 'srbtranslatin-settings', \plugins_url( 'assets/js/settings.js', \STL_FILE ), array(), \STL_VER, true );
        $tabs = \array_column( $this->config['tabs'], 'title', 'id' );
        $tab  = isset( $_GET['tab'] ) && \is_string( $_GET['tab'] ) ? \sanitize_key( \wp_unslash( $_GET['tab'] ) ) : '';
        $tab  = \array_key_exists( $tab, $tabs ) ? $tab : (string) \array_key_first( $tabs );
        $values = $this->saved_values();

        echo '<div class="wrap"><h1>' . \esc_html( $page['page_title'] ) . '</h1>';
        \settings_errors( $page['option_name'] );
        echo '<nav class="nav-tab-wrapper" aria-label="' . \esc_attr__( 'Settings sections', 'srbtranslatin' ) . '">';
        foreach ( $tabs as $id => $title ) {
            $url = \add_query_arg( array( 'page' => $page['slug'], 'tab' => $id ), \admin_url( $page['parent_slug'] ) );
            echo '<a class="nav-tab' . ( $id === $tab ? ' nav-tab-active' : '' ) . '" href="' . \esc_url( $url ) . '"' . ( $id === $tab ? ' aria-current="page"' : '' ) . '>' . \esc_html( $title ) . '</a>';
        }
        echo '</nav><form action="options.php" method="post">';
        \settings_fields( $page['slug'] );
        foreach ( $this->config['sections'] as $section ) {
            if ( ( $section['tab'] ?? '' ) !== $tab ) {
                continue;
            }
            echo '<h2>' . \esc_html( $section['title'] ) . '</h2><p>' . \esc_html( $section['description'] ?? '' ) . '</p><table class="form-table" role="presentation">';
            foreach ( $this->config['fields'] as $field ) {
                if ( $field['section'] === $section['id'] ) {
                    $this->render_field( $field, $values );
                }
            }
            echo '</table>';
        }
        \submit_button();
        echo '</form></div>';
    }

    /** @return array<string,mixed> */
    private function saved_values(): array {
        return WordPress_Option_Settings::saved_values( $this->config['page']['option_name'] );
    }

    /** @param array<string,mixed> $field Field schema. */
    private function is_disabled( array $field ): bool {
        return ! empty( $field['extras']['html_attributes']['disabled'] );
    }

    private function invalid_value(): void {
        \add_settings_error(
            $this->config['page']['option_name'],
            'srbtranslatin_invalid_value',
            \__( 'An invalid setting was submitted. Its previous value has been kept.', 'srbtranslatin' ),
        );
    }

    /**
     * @param array<string,mixed> $field  Field schema.
     * @param array<string,mixed> $values Saved options.
     */
    private function render_field( array $field, array $values ): void {
        $id       = 'stl-' . $field['id'];
        $name     = $this->config['page']['option_name'] . '[' . $field['id'] . ']';
        $value    = $values[ $field['id'] ] ?? $field['extras']['default'] ?? '';
        $disabled = $this->is_disabled( $field ) ? ' disabled' : '';
        $type     = $field['type'];
        $conditions = $field['extras']['conditions']['rules'] ?? array();
        echo '<tr data-stl-conditions="' . \esc_attr( (string) \wp_json_encode( $conditions ) ) . '"><th scope="row"><label for="' . \esc_attr( $id ) . '">' . \esc_html( $field['title'] ) . '</label></th><td>';
        if ( 'checkbox' === $type ) {
            if ( '' === $disabled ) {
                echo '<input type="hidden" name="' . \esc_attr( $name ) . '" value="0">';
            }
            echo '<input type="checkbox" id="' . \esc_attr( $id ) . '" name="' . \esc_attr( $name ) . '" value="1"' . \checked( (bool) $value, true, false ) . $disabled . '>';
        } elseif ( 'select' === $type || 'buttons_group' === $type ) {
            echo '<select id="' . \esc_attr( $id ) . '" name="' . \esc_attr( $name ) . '"' . $disabled . '>';
            foreach ( $field['extras']['options'] ?? array() as $key => $label ) {
                echo '<option value="' . \esc_attr( (string) $key ) . '"' . \selected( (string) $value, (string) $key, false ) . '>' . \esc_html( $label ) . '</option>';
            }
            echo '</select>';
        } elseif ( 'description' !== $type ) {
            echo '<input class="regular-text" type="text" id="' . \esc_attr( $id ) . '" name="' . \esc_attr( $name ) . '" value="' . \esc_attr( (string) $value ) . '"' . $disabled . '>';
        }
        if ( ! empty( $field['extras']['description'] ) ) {
            echo '<p class="description">' . \wp_kses_post( $field['extras']['description'] ) . '</p>';
        }
        echo '</td></tr>';
    }
}
