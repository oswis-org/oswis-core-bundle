<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Parent;

/**
 * Všechny obálky ze všech poskytovatelů — pro výběr „Vychází z" u šablony e-mailu.
 *
 * PROČ výběr, ne ruční cesta: od 23. 9. 2026 je pole rodičem šablony a systém k němu doplní
 * `{% extends %}`. Překlep v ručně psané cestě by znamenal šablonu, která nejde vykreslit.
 */
final class MailParentRegistry
{
    /** @var array<string, MailParent>|null */
    private ?array $parents = null;

    /** @param iterable<MailParentProviderInterface> $providers */
    public function __construct(private readonly iterable $providers)
    {
    }

    /** @return array<string, MailParent> Twig jméno → obálka; při shodě vyhrává dřívější poskytovatel */
    public function all(): array
    {
        if (null === $this->parents) {
            $this->parents = [];
            foreach ($this->providers as $provider) {
                foreach ($provider->getParents() as $parent) {
                    $this->parents[$parent->template] ??= $parent;
                }
            }
        }

        return $this->parents;
    }

    /**
     * Volby pro formulář: popisek → hodnota.
     *
     * Hodnota, kterou nabídka nezná (ručně zapsaná dřív, nebo obálka z modulu, který už neexistuje),
     * se do nabídky PŘIDÁ jako „jiné: …" — výběr nesmí tiše zahodit, co v datech je.
     *
     * @return array<string, string>
     */
    public function choices(?string $current = null): array
    {
        $choices = [];
        foreach ($this->all() as $parent) {
            $choices[$parent->label] = $parent->template;
        }
        if (null !== $current && '' !== trim($current) && !in_array($current, $choices, true)) {
            $choices['jiné: '.$current] = $current;
        }

        return $choices;
    }
}
