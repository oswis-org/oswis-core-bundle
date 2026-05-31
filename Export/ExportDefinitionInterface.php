<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Export;

/**
 * Definice exportovatelného seznamu: sloupce + metadata. Bez data-fetch logiky —
 * kolekci entit dodává volající kanál (web admin controller / API operace).
 */
interface ExportDefinitionInterface
{
    public function getKey(): string;            // 'participants'

    /** @return class-string */
    public function getResourceClass(): string;  // Participant::class

    public function getTitle(): string;          // 'Přehled přihlášek'

    /** @return list<ExportColumn> všechny dostupné sloupce v pořadí */
    public function getColumns(): array;
}
