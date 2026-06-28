<?php

declare(strict_types=1);

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

final class InstallWpTestsScriptTest extends TestCase {
    private string $tmp_dir;

    protected function set_up(): void {
        parent::set_up();

        $tmp = tempnam(sys_get_temp_dir(), 'stl-wp-cli-');

        if (false === $tmp) {
            $this->fail('Unable to create a temporary path.');
        }

        unlink($tmp);
        mkdir($tmp);

        $this->tmp_dir = $tmp;
    }

    protected function tear_down(): void {
        $this->removeDirectory($this->tmp_dir);

        parent::tear_down();
    }

    public function test_wp_cli_executes_composer_proxy_as_a_command(): void {
        $wp_bin = $this->tmp_dir . '/wp';

        file_put_contents(
            $wp_bin,
            <<<'BASH'
#!/usr/bin/env bash
if [ "$1" = "core" ] && [ "$2" = "version" ]; then
  printf '6.5.5\n'
  exit 0
fi

exit 12
BASH
        );
        chmod($wp_bin, 0755);

        $source = file_get_contents(dirname(__DIR__, 3) . '/bin/install-wp-tests.sh');

        if (false === $source) {
            $this->fail('Unable to read bin/install-wp-tests.sh.');
        }

        $probe = preg_replace('/\nmain "\$@"\s*$/', "\nwp_cli core version\n", $source, 1, $replacements);

        $this->assertSame(1, $replacements, 'Expected to replace the installer entry point.');
        $this->assertIsString($probe);

        $probe_path = $this->tmp_dir . '/probe.sh';
        file_put_contents($probe_path, $probe);
        chmod($probe_path, 0755);

        $result = $this->runProcess(['bash', $probe_path], [
            'PATH' => $this->tmp_dir . PATH_SEPARATOR . (string) getenv('PATH'),
        ]);

        $this->assertSame('', $result['stderr']);
        $this->assertSame(0, $result['exit_code']);
        $this->assertSame("6.5.5\n", $result['stdout']);
    }

    /**
     * @param list<string>          $command
     * @param array<string, string> $environment
     * @return array{exit_code: int, stdout: string, stderr: string}
     */
    private function runProcess(array $command, array $environment): array {
        $process = proc_open(
            $command,
            [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            dirname(__DIR__, 3),
            array_merge($_ENV, $environment),
        );

        if (! is_resource($process)) {
            $this->fail('Unable to start process.');
        }

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        return [
            'exit_code' => proc_close($process),
            'stdout'   => false === $stdout ? '' : $stdout,
            'stderr'   => false === $stderr ? '' : $stderr,
        ];
    }

    private function removeDirectory(string $path): void {
        if (! is_dir($path)) {
            return;
        }

        $items = scandir($path);

        if (false === $items) {
            return;
        }

        foreach ($items as $item) {
            if ('.' === $item || '..' === $item) {
                continue;
            }

            $child = $path . DIRECTORY_SEPARATOR . $item;

            if (is_dir($child) && ! is_link($child)) {
                $this->removeDirectory($child);
                continue;
            }

            unlink($child);
        }

        rmdir($path);
    }
}
