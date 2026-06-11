<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Export;

use OswisOrg\OswisCoreBundle\Enum\ExportFormat;

final class ExportRequest
{
    /**
     * @param list<string>|null $columnKeys vybrané sloupce v pořadí; null = výchozí sada
     * @param string|null       $subtitle   scope do hlavičky PDF (akce/typ/počet); null = bez podtitulku
     */
    public function __construct(
        public readonly ExportFormat $format,
        public readonly ?array $columnKeys = null,
        public readonly ?string $subtitle = null,
    ) {
    }
}
