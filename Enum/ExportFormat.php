<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Enum;

/**
 * Cílový formát exportu. CSV varianty mapují na CsvFormat.
 */
enum ExportFormat: string
{
    case CSV_EXCEL = 'csv';      // ; + UTF-8 BOM (český Excel)
    case CSV_RFC   = 'csv-rfc';  // , bez BOM (RFC 4180)
    case PDF       = 'pdf';

    public static function fromRequest(?string $value): self
    {
        return self::tryFrom((string) $value) ?? self::CSV_EXCEL;
    }

    public function isCsv(): bool
    {
        return self::PDF !== $this;
    }

    public function toCsvFormat(): CsvFormat
    {
        return self::CSV_RFC === $this ? CsvFormat::RFC_4180 : CsvFormat::EXCEL_CZ;
    }

    public function mimeType(): string
    {
        return self::PDF === $this ? 'application/pdf' : 'text/csv; charset=UTF-8';
    }

    public function fileExtension(): string
    {
        return self::PDF === $this ? 'pdf' : 'csv';
    }
}
