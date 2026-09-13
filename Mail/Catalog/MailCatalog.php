<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Catalog;

/**
 * Jediný serverový katalog proměnných a podmínek (spec 2026-09-13 §3.4). Krmí editor
 * ({@see \OswisOrg\OswisCoreBundle\Mail\Editor\MailEditorConfig}) a kontrolu chyb (výraz mimo
 * katalog = varování). Vložené bloky má {@see \OswisOrg\OswisCoreBundle\Mail\Block\MailBlockRegistry}.
 */
final class MailCatalog
{
    /** @param iterable<MailCatalogProviderInterface> $providers */
    public function __construct(private readonly iterable $providers)
    {
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
}
