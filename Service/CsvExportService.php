<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Service;

use League\Csv\Bom;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception as CsvException;
use League\Csv\Writer;
use OswisOrg\OswisCoreBundle\Enum\CsvFormat;

/**
 * Builds RFC 4180-correct CSV strings via League\Csv, so values containing
 * delimiters, double quotes or newlines (e.g. names like O'Brien, "Smith, Jr.")
 * can no longer break the column layout.
 *
 * Use this instead of hand-rolled string concatenation in any new export.
 */
final class CsvExportService
{
    /**
     * @param list<string>                              $header
     * @param iterable<array<int|string, scalar|null>>  $rows
     *
     * @throws CsvException
     * @throws CannotInsertRecord
     */
    public function build(array $header, iterable $rows, CsvFormat $format = CsvFormat::EXCEL_CZ): string
    {
        $writer = Writer::fromString();
        $writer->setDelimiter($format->delimiter());
        $writer->setEnclosure('"');
        $writer->setEndOfLine("\r\n");
        if ($format->useBom()) {
            $writer->setOutputBOM(Bom::Utf8);
        }
        $writer->insertOne(array_map(static fn (mixed $cell): string => self::stringify($cell), $header));
        foreach ($rows as $row) {
            $writer->insertOne(array_map(static fn (mixed $cell): string => self::stringify($cell), $row));
        }

        return $writer->toString();
    }

    private static function stringify(mixed $value): string
    {
        if (null === $value) {
            return '';
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_scalar($value)) {
            return (string) $value;
        }

        return '';
    }
}
