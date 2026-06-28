<?php
/**
 * TranslitHandlerTest class file.
 *
 * @package SrbTransLatin
 * @subpackage Tests
 */

namespace STL\Tests\Unit\Translit;

use PHPUnit\Framework\TestCase;
use STL\Common\Settings\Array_Settings;
use STL\Translit\Handlers\Translit_Handler;
use STL\Translit\Services\Script_Manager;
use STL\Translit\Services\Translit_Service;
use XWP\DI\Decorators\Action;
use XWP\DI\Decorators\Filter;

require_once dirname(__DIR__, 2) . '/unit-bootstrap.php';

/**
 * Test translit handler behavior and wiring.
 */
final class TranslitHandlerTest extends TestCase {
    protected function setUp(): void {
        $_GET = [];
    }

    /**
     * @return void
     */
    public function test_can_initialize_returns_true_when_transliteration_is_enabled(): void {
        $manager = $this->create_manager('lat');

        self::assertTrue(Translit_Handler::can_initialize($manager));
    }

    /**
     * @return void
     */
    public function test_can_initialize_returns_false_when_transliteration_is_disabled(): void {
        $manager = $this->create_manager('cir');

        self::assertFalse(Translit_Handler::can_initialize($manager));
    }

    /**
     * @return void
     */
    public function test_buffer_start_uses_service_buffer_callback(): void {
        $manager = $this->create_manager('lat');
        $service = new Translit_Service($manager);
        $handler = new Translit_Handler($service);

        ob_start();
        $handler->buffer_start();
        echo 'Ћирилица';
        ob_end_flush();
        self::assertSame('Ćirilica', ob_get_clean());
    }

    /**
     * @return void
     */
    public function test_ajax_buffer_start_uses_service_ajax_callback(): void {
        $manager = $this->create_manager('lat');
        $service = new Translit_Service($manager);
        $handler = new Translit_Handler($service);

        ob_start();
        $handler->ajax_buffer_start();
        echo '{"title":"Ћирилица"}';
        ob_end_flush();
        self::assertSame('{"title":"Ćirilica"}', ob_get_clean());
    }

    /**
     * @return void
     */
    public function test_translate_delegates_to_the_service(): void {
        $manager = $this->create_manager('lat');
        $service = new Translit_Service($manager);
        $handler = new Translit_Handler($service);

        self::assertSame('Ćirilica', $handler->translate('Ћирилица'));
    }

    /**
     * @return void
     */
    public function test_is_wc_ajax_request_detects_request_flag(): void {
        $_GET['wc-ajax'] = 'get_refreshed_fragments';

        self::assertTrue(Translit_Handler::is_wc_ajax_request());
    }

    /**
     * @return void
     */
    public function test_should_buffer_ajax_reads_fix_ajax_setting(): void {
        $page = new Array_Settings(
            [
                'fix_ajax' => true,
            ],
        );

        self::assertTrue(Translit_Handler::should_buffer_ajax($page));
    }

    /**
     * @return void
     */
    public function test_buffer_start_uses_decorated_frontend_actions(): void {
        $hooks = array_map(
            static fn($attribute) => $attribute->newInstance()->tag,
            (new \ReflectionMethod(Translit_Handler::class, 'buffer_start'))->getAttributes(Action::class)
        );

        self::assertSame(array('wp_head', 'rss_head', 'atom_head', 'rdf_head', 'rss2_head'), $hooks);
    }

    /**
     * @return void
     */
    public function test_ajax_buffer_start_uses_decorated_ajax_and_wc_ajax_actions(): void {
        $attributes = array_map(
            static fn($attribute) => $attribute->newInstance(),
            (new \ReflectionMethod(Translit_Handler::class, 'ajax_buffer_start'))->getAttributes(Action::class)
        );

        self::assertCount(2, $attributes);
        self::assertSame('admin_init', $attributes[0]->tag);
        self::assertSame(-9999, $attributes[0]->priority);
        self::assertSame('template_redirect', $attributes[1]->tag);
        self::assertSame(-1, $attributes[1]->priority);
    }

    /**
     * @return void
     */
    public function test_translate_uses_decorated_gettext_filters(): void {
        $hooks = array_map(
            static fn($attribute) => $attribute->newInstance()->tag,
            (new \ReflectionMethod(Translit_Handler::class, 'translate'))->getAttributes(Filter::class)
        );

        self::assertSame(array('gettext', 'ngettext', 'gettext_with_context', 'ngettext_with_context'), $hooks);
    }

    private function create_manager(string $script, string $locale = 'sr_RS'): Script_Manager {
        $manager = new Script_Manager(
            'both',
            'cir',
            $script,
            '',
            'pismo',
            null,
            null,
            static fn(): string => $locale
        );
        $manager->initialize();

        return $manager;
    }
}
