<?php

namespace SGI\STL\Update;

use function SGI\STL\Utils\{
    getVersion,
    getOptions
};

/**
 * Class which handles plugin updates and migrations (STL-old -> STL and SRLat -> STL)
 * @since 2.0
 * @author SG
 */
class Handler
{

    /**
     * @var array $stl_old - Array containing old options for STL (< 2.0)
     * @since 2.0
     */
    private $stl_old;

    /**
     * @var array $srlat - Array containing old options for SRLat (<2.0)
     */
    private $srlat;

    /**
     * @var array $stl - Array containing new and migrated ooptions for STL
     */
    private $stl;

    /**
     * @var string $stl_ver - currently installed version of the plugin
     */
    private $stl_ver;

    /**
         * Constructor instantiates the class, and then runs the migration if necessary.
         * In newer versions, it will run the updater functions if needed
         * @return void
         * @since 2.0
         * @author SG
         */    
    public function __construct()
    {

        $this->stl     = getOptions();
        $this->stl_ver = getVersion();

        $migrated = get_option('sgi/stl/migrated');

        if (!$migrated) :

            $this->stl_old = $this->get_legacy_stl_config();
            $this->srlat   = $this->get_legacy_srlat_config();

            $this->do_migration();

        endif;

    }

    private function get_legacy_stl_config()
    {

        $opts = [];
        $false_cnt = 0;

        $arr = [
            'stl_use_cookie',
            'stl_lang_identificator',
            'stl_cir_id',
            'stl_lat_id',
            'stl_use_russian',
            'stl_sanitize_file_names',
            'stl_skip_files',
            'file_lang_delimiter',
            'stl_default_language'
        ];

        foreach ($arr as $opt) :

            $db_opt = get_option($opt);

            $false_cnt += (!$db_opt) ? 1 : 0;

            $opts[$opt] = $db_opt;

        endforeach;

        return ($false_cnt == 9) ? false : $opts;

    }

    private function get_legacy_srlat_config()
    {

        return (get_option('sgi_srlat_ver')) ? get_option('sgi_srlat_opts') : false;

    }

    public function do_migration()
    {

        if (is_array($this->stl_old))
            $this->migrate_legacy_stl();

        if (is_array($this->srlat))
            $this->migrate_legacy_srlat();

        
        $this->finalize_migration();

    }

    private function migrate_legacy_stl()
    {

        $this->stl['core']['script'] = $this->stl_old['stl_default_language'];
        $this->stl['core']['param']  = $this->stl_old['stl_lang_identificator'];
        $this->stl['file']['names']  = ($this->stl_old['stl_sanitize_file_names']) ? true : false;
        $this->stl['file']['delim']  = $this->stl_old['file_lang_delimiter'];

    }

    private function cleanup_legacy_stl()
    {

        global $wpdb;

        $prefix = $wpdb->prefix;

        $sql = "DELETE FROM {$prefix}options WHERE option_name LIKE 'stl_%' OR option_name = 'file_lang_delimiter'";

        $wpdb->query($sql);

        delete_option('widget_stl_scripts_widget');

    }

    private function migrate_legacy_srlat()
    {

        $this->stl['core'] = [
            'script' => $this->srlat['core']['script'],
            'param'  => $this->stl['core']['param']
        ];

        $this->stl['file']['names'] = $this->srlat['core']['fix_media'];

        $this->stl['fixes'] = [
            'search'     => $this->srlat['core']['fix_search'],
            'permalinks' => $this->srlat['core']['fix_permalinks']
        ];
        
        $this->stl['ml'] = [
            'wpml' => $this->srlat['wpml']['extend_lang'],
            'pll'  => $this->srlat['polylang']['extend_lang'],
            'qtx'  => $this->srlat['qtx']['extend_lang']
        ];

    }

    private function cleanup_legacy_srlat()
    {

        delete_option('sgi_srlat_ver');
        delete_option('sgi_srlat_opts');
        delete_option('widget_sgi_srlat_widget');

    }

    private function finalize_migration()
    {

        $this->stl['migrated'] = true;

        update_option('sgi/stl/migrated', 1);
        update_option('sgi/stl/ver', $this->stl_ver);
        update_option('sgi/stl/opt', $this->stl);

        $this->cleanup_legacy_stl();
        $this->cleanup_legacy_srlat();

    }

}