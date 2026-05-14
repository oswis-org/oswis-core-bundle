<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Twig\Extension;

use RuntimeException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig filter that converts MJML markup into responsive HTML emails.
 *
 * Pipes the rendered MJML string into the official `mjml` Node CLI (must be
 * installed at `node_modules/.bin/mjml` under the project root, or path
 * overridden via the `OSWIS_MJML_BINARY` env var). Compiled HTML is cached
 * keyed by xxh3 hash of the input — the Node process is only invoked when
 * the input markup actually changes (so production rendering stays fast and
 * doesn't depend on Node being responsive on the request path after the
 * cache warms).
 *
 * Use as the wrapper filter on entire e-mail templates:
 *   {% apply mjml_to_html %}<mjml>…</mjml>{% endapply %}
 */
final class MjmlExtension extends AbstractExtension
{
    private const DEFAULT_BINARY_RELATIVE = 'node_modules/.bin/mjml';

    private const PROCESS_TIMEOUT_SECONDS = 30.0;

    private const CACHE_KEY_PREFIX = 'oswis.mjml.';

    private const CACHE_TTL_SECONDS = 60 * 60 * 24 * 7;

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly string $projectDir,
        private readonly ?string $binaryPathOverride = null,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'mjml_to_html',
                $this->mjmlToHtml(...),
                ['is_safe' => ['html']],
            ),
        ];
    }

    public function mjmlToHtml(string $mjml): string
    {
        if ('' === trim($mjml)) {
            return '';
        }
        $cacheKey = self::CACHE_KEY_PREFIX.hash('xxh3', $mjml);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($mjml): string {
            $item->expiresAfter(self::CACHE_TTL_SECONDS);

            return $this->compile($mjml);
        });
    }

    private function compile(string $mjml): string
    {
        $binary = $this->resolveBinary();
        $process = new Process([$binary, '-i', '-s'], $this->projectDir);
        $process->setInput($mjml);
        $process->setTimeout(self::PROCESS_TIMEOUT_SECONDS);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    private function resolveBinary(): string
    {
        $candidate = $this->binaryPathOverride
            ?? ($this->projectDir.DIRECTORY_SEPARATOR.self::DEFAULT_BINARY_RELATIVE);
        if (!is_executable($candidate)) {
            throw new RuntimeException(
                "MJML binary not found or not executable at '$candidate'. "
                ."Run 'npm install' (mjml is in package.json), or set OSWIS_MJML_BINARY env var.",
            );
        }

        return $candidate;
    }
}
