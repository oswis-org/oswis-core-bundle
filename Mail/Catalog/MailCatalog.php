<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Catalog;

use OswisOrg\OswisCoreBundle\Mail\Block\MailBlockRegistry;

/**
 * Jediný serverový katalog proměnných, podmínek a bloků (spec 2026-09-13 §3.4). Krmí nabídku
 * „Vložit", kontrolu chyb (výraz mimo katalog = varování) a od dávky 2 i editor.
 */
final class MailCatalog
{
    public const string BLOCKS_GROUP = 'Vložené bloky';

    /** @param iterable<MailCatalogProviderInterface> $providers */
    public function __construct(
        private readonly iterable $providers,
        private readonly MailBlockRegistry $blocks,
    ) {
    }

    /** @return list<MailCatalogItem> */
    public function items(): array
    {
        $items = [];
        foreach ($this->providers as $provider) {
            foreach ($provider->getItems() as $item) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /** @return list<string> */
    public function rootNames(): array
    {
        $names = [];
        foreach ($this->items() as $item) {
            $name = $item->rootName();
            if ('' !== $name && !in_array($name, $names, true)) {
                $names[] = $name;
            }
        }

        return $names;
    }

    /** Smí výraz zůstat u části příjemců prázdný? (Jen když to říká položka katalogu.) */
    public function mayBeEmpty(string $expression): bool
    {
        $normalized = MailCatalogItem::normalize($expression);
        foreach ($this->items() as $item) {
            if ($item->mayBeEmpty && MailCatalogItem::normalize($item->expression) === $normalized) {
                return true;
            }
        }

        return false;
    }

    /** @return array<string, list<array{label: string, token: string}>> */
    public function groupedForPanel(): array
    {
        $groups = [];
        foreach ($this->items() as $item) {
            $groups[$item->group][] = ['label' => $item->label, 'token' => $item->token()];
        }
        foreach ($this->blocks->all() as $block) {
            $groups[self::BLOCKS_GROUP][] = ['label' => $block->label, 'token' => sprintf("{{ blok('%s') }}", $block->key)];
        }

        return $groups;
    }
}
