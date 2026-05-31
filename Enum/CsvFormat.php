<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Enum;

/**
 * CSV output presets for exports.
 *
 * - EXCEL_CZ: delimiter ';' + UTF-8 BOM. Czech Excel opens such a file
 *   correctly on double-click (diacritics intact, columns split). Default.
 * - RFC_4180: delimiter ',' + no BOM. Strict RFC 4180 for universal import
 *   into other systems / scripts.
 *
 * Both presets quote with '"' and let League\Csv handle escaping of
 * delimiters, quotes and newlines inside values.
 */
enum CsvFormat: string
{
    case EXCEL_CZ = 'excel';
    case RFC_4180 = 'rfc';

    /**
     * Resolve from an untrusted request value, falling back to the CZ-Excel
     * default for anything unknown or empty.
     */
    public static function fromRequest(?string $value): self
    {
        return self::tryFrom((string) $value) ?? self::EXCEL_CZ;
    }

    public function delimiter(): string
    {
        return self::EXCEL_CZ === $this ? ';' : ',';
    }

    public function useBom(): bool
    {
        return self::EXCEL_CZ === $this;
    }

    public function label(): string
    {
        return match ($this) {
            self::EXCEL_CZ => 'Excel (CZ) — středník + BOM',
            self::RFC_4180 => 'RFC 4180 — čárka, bez BOM',
        };
    }
}
