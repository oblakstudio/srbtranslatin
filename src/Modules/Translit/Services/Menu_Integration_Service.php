<?php
/**
 * Menu_Integration_Service class file.
 *
 * @package SrbTransLatin
 * @subpackage Translit
 */

namespace STL\Translit\Services;

use stdClass;

/**
 * Integrate the script selector into classic and block menus.
 */
final class Menu_Integration_Service {
    /**
     * Constructor.
     *
     * @param Script_Manager $script_manager Script manager runtime state.
     * @param bool           $extend Whether menu extension is enabled.
     * @param string         $target_location Menu location slug to extend.
     * @param string         $selector_type Selector type (`submenu` or `inline`).
     * @param string         $menu_title Selector root title for submenu mode.
     */
    public function __construct(
        private Script_Manager $script_manager,
        private bool $extend,
        private string $target_location,
        private string $selector_type,
        private string $menu_title,
    ) {
    }

    /**
     * Inject selector items into a classic menu.
     *
     * @param array<int,object> $items Existing menu items.
     * @param object            $menu Current menu object.
     * @return array<int,object>
     */
    public function inject_classic_menu_selector( array $items, object $menu ): array {
        if ( ! $this->should_extend_classic_menu( $menu ) ) {
            return $items;
        }

        $menu_order = 2000;
        $current    = 2000;
        $selector   = $this->build_classic_selector_items( $menu_order, $current );

        return \array_merge( $items, $selector );
    }

    /**
     * Inject selector markup into navigation block output.
     *
     * @param string              $block_content Navigation block HTML.
     * @param array<string,mixed> $block Block data.
     * @return string
     */
    public function inject_navigation_block_selector( string $block_content, array $block ): string {
        if ( ! $this->can_extend_menu_selector() ) {
            return $block_content;
        }

        if ( isset( $block['blockName'] ) && 'core/navigation' !== $block['blockName'] ) {
            return $block_content;
        }

        if ( \str_contains( $block_content, 'stl-script-selector-block' ) || \str_contains(
            $block_content,
            'stl-block-selector',
        ) ) {
            return $block_content;
        }

        $selector_markup = $this->build_block_selector_markup(
            '',
            $this->get_default_block_display_mode(),
            $this->get_default_block_labels(),
        );

        if ( \str_contains( $block_content, '</nav>' ) ) {
            return \preg_replace(
                '/<\/nav>$/',
                $selector_markup . '</nav>',
                $block_content,
            ) ?? $block_content . $selector_markup;
        }

        return $block_content . $selector_markup;
    }

    /**
     * Register selector block for site editor and navigation block usage.
     *
     * @return void
     */
    public function register_selector_block(): void {
        if ( ! \function_exists( 'register_block_type' ) ) {
            return;
        }

        if ( $this->is_test_block_registry_available() ) {
            $this->register_block_type(
                \STL_PATH . 'assets/blocks/script-selector/build',
                array(
                    'render_callback' => array( $this, 'render_selector_block' ),
                ),
            );

            return;
        }

        if (
            \class_exists( '\WP_Block_Type_Registry' ) &&
            \method_exists( '\WP_Block_Type_Registry', 'get_instance' ) &&
            \WP_Block_Type_Registry::get_instance()->is_registered( 'srbtranslatin/script-selector' )
        ) {
            return;
        }

        $block_path = \STL_PATH . 'assets/blocks/script-selector/build';

        if ( ! \is_dir( $block_path ) ) {
            return;
        }

        $this->register_block_type(
            $block_path,
            array(
                'render_callback' => array( $this, 'render_selector_block' ),
            ),
        );
    }

    /**
     * Render selector block output.
     *
     * @param array<string,mixed> $attributes Block attributes.
     * @param string              $content Block content.
     * @return string
     */
    public function render_selector_block( array $attributes = array(), string $content = '' ): string {
        if ( ! $this->can_render_block_selector() ) {
            return '';
        }

        return $this->build_block_selector_markup(
            'stl-script-selector-block',
            $this->resolve_block_display_mode( $attributes ),
            $this->resolve_block_labels( $attributes ),
        );
    }

    /**
     * Render legacy helper-compatible selector markup.
     *
     * @param array<string,mixed> $args Legacy helper arguments.
     * @return string
     */
    public function render_compat_selector( array $args = array() ): string {
        if ( ! $this->can_render_block_selector() ) {
            return '';
        }

        $labels = array(
            'cir' => isset( $args['cir_caption'] ) && '' !== (string) $args['cir_caption'] ? (string) $args['cir_caption'] : $this->get_available_scripts()['cir'],
            'lat' => isset( $args['lat_caption'] ) && '' !== (string) $args['lat_caption'] ? (string) $args['lat_caption'] : $this->get_available_scripts()['lat'],
        );
        $separator = isset( $args['separator'] ) ? (string) $args['separator'] : '<span>&nbsp; | &nbsp;</span>';
        $mode      = $this->normalize_compat_selector_mode( (string) ( $args['selector_type'] ?? 'online' ) );
        $scripts   = $this->build_compat_selector_links( $labels, $args );

        if ( 'dropdown' !== $mode && ! empty( $args['inactive_only'] ) ) {
            $scripts = \array_values(
                \array_filter(
                    $scripts,
                    static fn( array $script ): bool => ! $script['active']
                )
            );
        }

        $markup = match ( $mode ) {
            'list' => $this->render_list_selector_markup( $scripts ),
            'dropdown' => $this->render_dropdown_selector_markup( $scripts ),
            default => $this->render_inline_selector_markup( $scripts, $separator ),
        };

        return '<div class="stl-script-selector">' . $markup . '</div>';
    }

    /**
     * Build selector items for classic menus.
     *
     * @param int $menu_order Base order.
     * @param int $current Base current value.
     * @return array<int,object>
     */
    private function build_classic_selector_items( int $menu_order, int $current ): array {
        if ( 'submenu' !== $this->selector_type ) {
            return $this->build_inline_items( $menu_order, $current );
        }

        $root = $this->create_menu_item( $this->menu_title, '#', ++$menu_order, 0, false );

        return \array_merge(
            array( $root ),
            $this->build_inline_items( $menu_order, $current, (int) $root->ID ),
        );
    }

    /**
     * Build inline selector items for classic menus.
     *
     * @param int $menu_order Base order.
     * @param int $current Base current value.
     * @param int $parent Menu parent ID.
     * @return array<int,object>
     */
    private function build_inline_items( int $menu_order, int $current, int $parent = 0 ): array {
        $items         = array();
        $active_script = $this->script_manager->get_script();

        foreach ( $this->get_available_scripts() as $id => $title ) {
            $items[] = $this->create_menu_item(
                $title,
                $this->build_script_url( $id ),
                ++$menu_order,
                $parent,
                $id === $active_script,
            );
        }

        return $items;
    }

    /**
     * Create a menu item object.
     *
     * @param string $title Menu title.
     * @param string $url Menu URL.
     * @param int    $order Menu order.
     * @param int    $menu_parent Menu parent ID.
     * @param bool   $is_current Whether item is current.
     * @return object
     */
    private function create_menu_item( string $title, string $url, int $order, int $menu_parent, bool $is_current ): object {
        $item = new stdClass();

        $item->ID               = 100000 + $order + $menu_parent;
        $item->db_id            = $item->ID;
        $item->title            = $title;
        $item->url              = $url;
        $item->menu_order       = $order;
        $item->menu_item_parent = $menu_parent;
        $item->type             = '';
        $item->object           = '';
        $item->object_id        = '';
        $item->classes          = array();
        $item->target           = '';
        $item->attr_title       = '';
        $item->description      = '';
        $item->xfn              = '';
        $item->status           = '';
        $item->current          = $is_current;

        return \wp_setup_nav_menu_item( $item );
    }

    /**
     * Build block selector markup.
     *
     * @param array<string,string> $labels Script labels keyed by script code.
     * @return string
     */
    private function build_block_selector_markup( string $extra_class = '', string $display_mode = 'inline', array $labels = array() ): string {
        $container_class = 'stl-block-selector';

        if ( '' !== $extra_class ) {
            $container_class .= ' ' . $extra_class;
        }

        $container_class .= ' is-mode-' . $display_mode;
        $scripts          = $this->build_block_selector_links( $labels );

        return \sprintf(
            '<div class="%s">%s</div>',
            $container_class,
            match ( $display_mode ) {
                'list' => $this->render_list_selector_markup( $scripts ),
                'dropdown' => $this->render_dropdown_selector_markup( $scripts ),
                default => $this->render_inline_selector_markup( $scripts ),
            },
        );
    }

    /**
     * Resolve the display mode for the explicit selector block.
     *
     * @param array<string,mixed> $attributes Block attributes.
     * @return string
     */
    private function resolve_block_display_mode( array $attributes ): string {
        $mode = \sanitize_key( (string) ( $attributes['displayMode'] ?? '' ) );

        return \in_array( $mode, array( 'inline', 'list', 'dropdown' ), true )
            ? $mode
            : $this->get_default_block_display_mode();
    }

    /**
     * Resolve block labels, falling back to translated defaults.
     *
     * @param array<string,mixed> $attributes Block attributes.
     * @return array{cir:string,lat:string}
     */
    private function resolve_block_labels( array $attributes ): array {
        $labels = $this->get_default_block_labels();

        if ( isset( $attributes['cyrillicLabel'] ) ) {
            $value = \sanitize_text_field( (string) $attributes['cyrillicLabel'] );

            if ( '' !== $value ) {
                $labels['cir'] = $value;
            }
        }

        if ( isset( $attributes['latinLabel'] ) ) {
            $value = \sanitize_text_field( (string) $attributes['latinLabel'] );

            if ( '' !== $value ) {
                $labels['lat'] = $value;
            }
        }

        return $labels;
    }

    /**
     * Get the default display mode for the selector block.
     *
     * @return string
     */
    private function get_default_block_display_mode(): string {
        return 'inline';
    }

    /**
     * Get the default block labels.
     *
     * @return array{cir:string,lat:string}
     */
    private function get_default_block_labels(): array {
        return $this->get_available_scripts();
    }

    /**
     * Build normalized selector link data for block rendering.
     *
     * @param array{cir:string,lat:string} $labels Resolved script labels.
     * @return array<int,array{name:string,label:string,url:string,active:bool}>
     */
    private function build_block_selector_links( array $labels ): array {
        return $this->build_selector_links( $labels );
    }

    /**
     * Render inline selector markup.
     *
     * @param array<int,array{name:string,label:string,url:string,active:bool}> $scripts Script link data.
     * @return string
     */
    private function render_inline_selector_markup( array $scripts, string $separator = '' ): string {
        $markup = '';
        $total  = \count( $scripts );

        foreach ( $scripts as $index => $script ) {
            $markup .= $this->render_selector_link( $script );

            if ( '' !== $separator && $index < $total - 1 ) {
                $markup .= $separator;
            }
        }

        return $markup;
    }

    /**
     * Render list selector markup.
     *
     * @param array<int,array{name:string,label:string,url:string,active:bool}> $scripts Script link data.
     * @return string
     */
    private function render_list_selector_markup( array $scripts ): string {
        $items = '';

        foreach ( $scripts as $script ) {
            $items .= \sprintf(
                '<li class="stl-script-selector-item">%s</li>',
                $this->render_selector_link( $script ),
            );
        }

        return \sprintf( '<ul class="stl-script-selector-list">%s</ul>', $items );
    }

    /**
     * Render dropdown selector markup.
     *
     * @param array<int,array{name:string,label:string,url:string,active:bool}> $scripts Script link data.
     * @return string
     */
    private function render_dropdown_selector_markup( array $scripts ): string {
        $options = '';

        foreach ( $scripts as $script ) {
            $selected = $script['active'] ? ' selected="selected"' : '';
            $options .= \sprintf(
                '<option value="%s"%s>%s</option>',
                \htmlspecialchars( $script['url'], \ENT_QUOTES, 'UTF-8' ),
                $selected,
                \htmlspecialchars( $script['label'], \ENT_QUOTES, 'UTF-8' ),
            );
        }

        return \sprintf(
            '<select class="stl-script-selector-select" onchange="window.location.href=this.value">%s</select>',
            $options,
        );
    }

    /**
     * Render a selector link.
     *
     * @param array{name:string,label:string,url:string,active:bool} $script Script link data.
     * @return string
     */
    private function render_selector_link( array $script ): string {
        $class = 'stl-script-link' . ( $script['active'] ? ' is-active' : '' );

        return \sprintf(
            '<a class="%s" href="%s">%s</a>',
            $class,
            \htmlspecialchars( $script['url'], \ENT_QUOTES, 'UTF-8' ),
            \htmlspecialchars( $script['label'], \ENT_QUOTES, 'UTF-8' ),
        );
    }

    /**
     * Check if selector can be rendered.
     *
     * @return bool
     */
    private function can_extend_menu_selector(): bool {
        return $this->extend && $this->script_manager->allows_selector();
    }

    /**
     * Check if the explicit selector block can be rendered.
     *
     * @return bool
     */
    private function can_render_block_selector(): bool {
        return $this->script_manager->allows_selector();
    }

    /**
     * Check if classic menu should be extended.
     *
     * @param object $menu Menu object.
     * @return bool
     */
    private function should_extend_classic_menu( object $menu ): bool {
        if ( ! $this->can_extend_menu_selector() || '' === $this->target_location ) {
            return false;
        }

        $locations = $this->get_nav_menu_locations();

        if ( ! isset( $locations[ $this->target_location ] ) ) {
            return false;
        }

        return (int) $locations[ $this->target_location ] === (int) ( $menu->term_id ?? 0 );
    }

    /**
     * Get available scripts for selector links.
     *
     * @return array<string,string>
     */
    private function get_available_scripts(): array {
        return array(
            'cir' => \__( 'Cyrillic', 'srbtranslatin' ),
            'lat' => \__( 'Latin', 'srbtranslatin' ),
        );
    }

    /**
     * Build selector link data with optional compatibility overrides.
     *
     * @param array{cir:string,lat:string} $labels Selector labels.
     * @param array<string,mixed>          $overrides Legacy overrides.
     * @return array<int,array{name:string,label:string,url:string,active:bool}>
     */
    private function build_selector_links( array $labels, array $overrides = array() ): array {
        $active_script = isset( $overrides['active_script'] ) && \in_array( $overrides['active_script'], array( 'cir', 'lat' ), true )
            ? (string) $overrides['active_script']
            : $this->script_manager->get_script();

        return array(
            array(
                'name'   => 'cir',
                'label'  => $labels['cir'],
                'url'    => isset( $overrides['cir_link'] ) ? (string) $overrides['cir_link'] : $this->build_script_url( 'cir' ),
                'active' => 'cir' === $active_script,
            ),
            array(
                'name'   => 'lat',
                'label'  => $labels['lat'],
                'url'    => isset( $overrides['lat_link'] ) ? (string) $overrides['lat_link'] : $this->build_script_url( 'lat' ),
                'active' => 'lat' === $active_script,
            ),
        );
    }

    /**
     * Build selector links for the legacy helper path.
     *
     * @param array{cir:string,lat:string} $labels Selector labels.
     * @param array<string,mixed>          $args Helper arguments.
     * @return array<int,array{name:string,label:string,url:string,active:bool}>
     */
    private function build_compat_selector_links( array $labels, array $args ): array {
        return $this->build_selector_links( $labels, $args );
    }

    /**
     * Normalize legacy selector types to the supported display modes.
     *
     * @param string $selector_type Legacy selector type.
     * @return string
     */
    private function normalize_compat_selector_mode( string $selector_type ): string {
        $selector_type = \sanitize_key( $selector_type );

        return match ( $selector_type ) {
            'list' => 'list',
            'dropdown' => 'dropdown',
            'oneline', 'online', 'inline' => 'inline',
            default => 'inline',
        };
    }

    /**
     * Build a script switching URL.
     *
     * @param string $script Script identifier.
     * @return string
     */
    private function build_script_url( string $script ): string {
        $current = (string) \xwp_fetch_server_var( 'REQUEST_URI', '/' );
        $parts   = \parse_url( $current );
        $path    = \is_array( $parts ) ? (string) ( $parts['path'] ?? '/' ) : '/';
        $query   = array();

        if ( '' === $path ) {
            $path = '/';
        }

        if ( \is_array( $parts ) && isset( $parts['query'] ) ) {
            \parse_str( $parts['query'], $query );
        }

        $query[ $this->script_manager->get_url_param() ] = $script;
        $query_string                                    = \http_build_query( $query );

        return $path . ( '' !== $query_string ? '?' . $query_string : '' );
    }

    /**
     * Resolve nav menu locations, allowing test doubles to override WordPress state.
     *
     * @return array<string,int>
     */
    private function get_nav_menu_locations(): array {
        if ( \array_key_exists( 'stl_test_nav_menu_locations', $GLOBALS ) ) {
            return (array) $GLOBALS['stl_test_nav_menu_locations'];
        }

        return \get_nav_menu_locations();
    }

    /**
     * Register a block type, allowing tests to capture registrations without WP registry side effects.
     *
     * @param string              $block_type Block metadata path.
     * @param array<string,mixed> $args Registration args.
     * @return void
     */
    private function register_block_type( string $block_type, array $args ): void {
        if ( $this->is_test_block_registry_available() ) {
            $GLOBALS['stl_test_registered_blocks'][] = array(
                'block_type' => $block_type,
                'args' => $args,
            );

            return;
        }

        \register_block_type( $block_type, $args );
    }

    /**
     * Check whether selector block registration should use the unit-test capture path.
     *
     * @return bool
     */
    private function is_test_block_registry_available(): bool {
        return \array_key_exists( 'stl_test_registered_blocks', $GLOBALS ) && \is_array( $GLOBALS['stl_test_registered_blocks'] );
    }
}
