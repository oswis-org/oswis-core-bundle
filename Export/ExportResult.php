<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Export;

final class ExportResult
{
    public function __construct(
        public readonly string $filename,
        public readonly string $mimeType,
        public readonly string $content,
    ) {
    }
}
