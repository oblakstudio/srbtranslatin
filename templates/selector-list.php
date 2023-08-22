<?php
/**
 * List script selector template
 *
 * @package SrbTransLatin
 * @subpackage Templates
 * @version 3.0.0
 */

?>
<?php foreach ( $scripts as $script ) : ?>
    <li>
        <a rel="nofollow" href="<?php echo esc_url( $script['link'] ); ?>" style="<?php echo ( $args['active_script'] === $script['name'] ) ? 'font-weight:700;' : ''; ?>">
            <?php echo esc_html( $script['caption'] ); ?>
        </a>
    </li>
<?php endforeach; ?>
</ul>
