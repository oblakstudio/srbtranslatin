<?php

use const SGI\STL\DOMAIN;

use function SGI\STL\Admin\Utils\{
    get_stl_settings,
    get_settings_template
};

$page = $_GET['page'] ?? 'stl_settings';

$tabs = [
    'stl_settings' => [
        'tab'  => __('Settings', DOMAIN),
        'file' => 'settings'
    ],
    'stl_status'   => [
        'tab'  => __('System Status', DOMAIN),
        'file' => 'status'
    ]
];


?>

<div id="srbtranslatin" class="wrap">

    <h1><?php echo get_admin_page_title();?></h1>

    <h2 class="nav-tab-wrapper">
    <?php foreach ($tabs as $tab => $data) : ?>

        <a href="<?php echo admin_url("admin.php?page={$tab}");?>" class="nav-tab <?php echo ($tab == $page) ? 'nav-tab-active' : ''; ?>"><?php echo $data['tab'];?></a>

    <?php endforeach; ?>
    </h2>

    <div id="poststuff" class="stl-wrapper">

        <div class="stl-content">

            

            <?php get_settings_template("parts/{$tabs[$page]['file']}");?>

        </div>

        <div class="stl-sidebar">
            <?php get_settings_template('parts/sidebar');?>
        </div>

    </div>

</div>