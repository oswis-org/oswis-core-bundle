<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Rendering;

use OswisOrg\OswisCoreBundle\Mail\Block\MailBlockRegistry;
use Twig\Environment;

/**
 * Dosadí vložené bloky za zástupné značky AŽ PO čištění textu (spec 2026-09-13 §3.3 krok 4).
 *
 * PROČ: bloky jsou důvěryhodný kód (rekapitulace, platební údaje) a nesou styly s významem —
 * přeškrtnutí zrušených položek. Čištění spolu s textem by je zahodilo.
 */
final class MailBlockRenderer
{
    public const string PLACEHOLDER_ELEMENT = 'oswis-block';

    private const string PLACEHOLDER_PATTERN = '~<oswis-block data-key="([A-Za-z0-9_-]+)"></oswis-block>~';

    public function __construct(
        private readonly Environment $twig,
        private readonly MailBlockRegistry $registry,
    ) {
    }

    /** Zástupná značka, kterou do textu vloží `{{ blok('klíč') }}`. */
    public static function placeholder(string $key): string
    {
        return sprintf('<%1$s data-key="%2$s"></%1$s>', self::PLACEHOLDER_ELEMENT, htmlspecialchars($key, ENT_QUOTES));
    }

    /** @return list<string> */
    public static function keysIn(string $html): array
    {
        preg_match_all(self::PLACEHOLDER_PATTERN, $html, $matches);

        return array_values(array_unique($matches[1]));
    }

    /** @param array<string, mixed> $context */
    public function replacePlaceholders(string $html, array $context): string
    {
        return (string) preg_replace_callback(
            self::PLACEHOLDER_PATTERN,
            function (array $match) use ($context): string {
                $block = $this->registry->get($match[1])
                    ?? throw new MailRenderingException(sprintf('Neznámý vložený blok „%s".', $match[1]));

                return $this->twig->render($block->template, $block->context($context));
            },
            $html,
        );
    }
}
