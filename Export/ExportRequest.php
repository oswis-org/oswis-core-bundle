<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Export;

use OswisOrg\OswisCoreBundle\Enum\ExportFormat;

final class ExportRequest
{
    /**
     * @param list<string>|null $columnKeys vybrané sloupce v pořadí; null = výchozí sada
     */
    public function __construct(
        public readonly ExportFormat $format,
        public readonly ?array $columnKeys = null,
    ) {
    }
}
