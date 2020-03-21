<form method="POST" action="options.php">

    <?php
    settings_fields('stl_settings');

    do_settings_sections('stl_settings');

    submit_button();
    ?>

</form>