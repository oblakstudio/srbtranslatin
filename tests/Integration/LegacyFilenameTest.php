<?php

declare(strict_types=1);

use STL\Common\Settings\Array_Settings;
use STL\Translit\Handlers\Filename_Handler;

final class LegacyFilenameTest extends WP_UnitTestCase {
    public function test_upload_names_are_transliterated_by_the_bootstrapped_plugin(): void {
        self::assertSame('Cirilicna-slika.jpg', sanitize_file_name('Ћирилична-слика.jpg'));
        self::assertSame('already-latin.jpg', sanitize_file_name('already-latin.jpg'));
    }

    public function test_upload_transliteration_can_be_disabled(): void {
        $handler = new Filename_Handler(new Array_Settings(['transliterate_uploads' => false]));
        self::assertSame('Ћирилица.jpg', $handler->transliterate_filename('Ћирилица.jpg'));
    }

    public function test_permalink_transliteration_runs_before_core_slug_encoding(): void {
        $handler = new Filename_Handler(new Array_Settings(['fix_permalinks' => true]));
        add_filter('sanitize_title', [$handler, 'transliterate_permalink'], 9, 3);
        try {
            self::assertSame('cirilicni-naslov', sanitize_title('Ћирилични наслов'));
            self::assertSame('already-latin', sanitize_title('Already Latin'));
            self::assertSame('%d1%9b%d0%b8%d1%80%d0%b8%d0%bb%d0%b8%d1%86%d0%b0', sanitize_title_for_query('Ћирилица'));
        } finally {
            remove_filter('sanitize_title', [$handler, 'transliterate_permalink'], 9);
        }
    }

    public function test_disabled_permalink_setting_preserves_the_input(): void {
        $handler = new Filename_Handler(new Array_Settings(['fix_permalinks' => false]));
        self::assertSame('Ћирилица', $handler->transliterate_permalink('Ћирилица'));
    }
}
