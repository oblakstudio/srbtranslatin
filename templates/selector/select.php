<?php
$scripts = [
    'cir',
    'lat'
];
?>
<select class="sgi-stl-select">
<?php foreach ($scripts as $script) : ?>

    <option value="<?php echo $args["{$script}_link"];?>" <?php selected($script, $args['active_script']);?> >
        <?php echo $args["{$script}_caption"];?>
    </option>

<?php endforeach;?>
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