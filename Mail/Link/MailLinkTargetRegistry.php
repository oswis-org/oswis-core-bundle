<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Link;

/** Všechny cíle odkazů ze všech poskytovatelů; při shodě klíče vyhrává dřívější (vyšší priorita tagu). */
final class MailLinkTargetRegistry
{
    /** @var array<string, MailLinkTarget>|null */
    private ?array $targets = null;

    /** @param iterable<MailLinkTargetProviderInterface> $providers */
    public function __construct(private readonly iterable $providers)
    {
    }

    public function get(string $key): ?MailLinkTarget
    {
        return $this->all()[$key] ?? null;
    }

    /** @return array<string, MailLinkTarget> */
    public function all(): array
    {
        if (null === $this->targets) {
            $this->targets = [];
            foreach ($this->providers as $provider) {
                foreach ($provider->getLinkTargets() as $target) {
                    $this->targets[$target->key] ??= $target;
                }
            }
        }

        return $this->targets;
    }

    /**
     * Pro editor — v pořadí, v jakém se mají ukázat v nabídce.
     *
     * @return list<array{key: string, label: string, group: string, parameter: string|null,
     *     options: list<array{value: string, label: string}>, hint: string|null}>
     */
    public function toArray(): array
    {
        return array_values(array_map(static fn (MailLinkTarget $target): array => $target->toArray(), $this->all()));
    }
}
