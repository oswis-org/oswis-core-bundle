<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Block;

/** Vložený blok do textu mailu — `{{ blok('key') }}` (spec 2026-09-13 §3.2). */
final readonly class MailBlock
{
    /**
     * @param string                                                      $key            klíč pro `{{ blok('…') }}`
     * @param string                                                      $label          český název v nabídce
     * @param string                                                      $template       Twig šablona bloku (soubor nebo slug v DB)
     * @param (\Closure(array<string, mixed>): array<string, mixed>)|null $contextBuilder doplní kontext bloku (např. `event`)
     */
    public function __construct(
        public string $key,
        public string $label,
        public string $template,
        public ?\Closure $contextBuilder = null,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function context(array $context): array
    {
        return null === $this->contextBuilder ? $context : array_merge($context, ($this->contextBuilder)($context));
    }
}
