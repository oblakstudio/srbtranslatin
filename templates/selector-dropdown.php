<?php
/**
 * Dropdown script selector template
 *
 * @package SrbTransLatin
 * @subpackage Templates
 * @version 3.0.0
 */

?>

<select class="sgi-stl-select">
<?php foreach ( $scripts as $script ) : ?>

    <option value="<?php echo esc_attr( $scripts['link'] ); ?>" <?php selected( $script, $args['active_script'] ); ?> >
        <?php echo esc_html( $args['caption'] ); ?>
    </option>

<?php endforeach; ?>
</select>
<script type="text/javascript">
(function($){
    $(document).ready(function(){
        $(document.body).on('change', '.sgi-stl-select', function(){
            location.href = $(this).val();
        });
    });
}) (jQuery);
</script>
