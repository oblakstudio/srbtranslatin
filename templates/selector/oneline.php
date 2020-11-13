<?php if ( !$args['inactive_only'] || ($args['active_script'] != 'cir') ) : ?>

    <?php $style = ($args['active_script'] == 'cir') ? 'font-weight:700;' : ''; ?>

    <a rel="nofollow" href="<?php echo $args['cir_link'];?>" style="<?php echo $style;?>">
        <?php echo $args['cir_caption'];?>
    </a>

<?php endif; ?>

<?php echo (!$args['inactive_only']) ? $args['separator'] : ''; ?>

<?php if ( !$args['inactive_only'] || ($args['active_script'] != 'lat') ) : ?>

    <?php $style = ($args['active_script'] == 'lat') ? 'font-weight:700;' : ''; ?>

    <a rel="nofollow" href="<?php echo $args['lat_link'];?>" style="<?php echo $style;?>">
        <?php echo $args['lat_caption'];?>
    </a>

<?php endif; ?>