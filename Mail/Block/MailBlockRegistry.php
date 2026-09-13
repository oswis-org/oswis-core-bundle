<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Block;

/** Všechny vložené bloky ze všech poskytovatelů; při shodě klíče vyhrává dřívější (vyšší priorita tagu). */
final class MailBlockRegistry
{
    /** @var array<string, MailBlock>|null */
    private ?array $blocks = null;

    /** @param iterable<MailBlockProviderInterface> $providers */
    public function __construct(private readonly iterable $providers)
    {
    }

    public function get(string $key): ?MailBlock
    {
        return $this->all()[$key] ?? null;
    }

    /** @return array<string, MailBlock> */
    public function all(): array
    {
        if (null === $this->blocks) {
            $this->blocks = [];
            foreach ($this->providers as $provider) {
                foreach ($provider->getBlocks() as $block) {
                    $this->blocks[$block->key] ??= $block;
                }
            }
        }

        return $this->blocks;
    }
}
