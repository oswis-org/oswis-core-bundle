<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Export;

use League\Csv\CannotInsertRecord;
use League\Csv\Exception as CsvException;
use Mpdf\MpdfException;
use OswisOrg\OswisCoreBundle\Service\CsvExportService;
use OswisOrg\OswisCoreBundle\Service\ExportService;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Vyrenderuje definici + kolekci entit + požadavek do souboru (CSV nebo PDF).
 */
final class ExportManager
{
    private const string PDF_TEMPLATE      = '@OswisOrgOswisCore/export/table.pdf.html.twig';
    private const int    PDF_LANDSCAPE_MIN = 8;

    public function __construct(
        private readonly CsvExportService $csvExportService,
        private readonly ExportService $exportService,
        private readonly Environment $twig,
    ) {
    }

    /**
     * @param iterable<mixed> $entities
     *
     * @throws CsvException
     * @throws CannotInsertRecord
     * @throws MpdfException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(ExportDefinitionInterface $definition, iterable $entities, ExportRequest $request): ExportResult
    {
        $columns = $this->selectColumns($definition, $request->columnKeys);
        $header = array_map(static fn (ExportColumn $c): string => $c->label, $columns);

        $rows = [];
        foreach ($entities as $entity) {
            if (!is_object($entity)) {
                continue;
            }
            $row = [];
            foreach ($columns as $column) {
                $row[] = $this->formatValue($column->extract($entity), $column->type);
            }
            $rows[] = $row;
        }

        $filename = $this->buildFilename($definition, $request);
        if ($request->format->isCsv()) {
            $content = $this->csvExportService->build($header, $rows, $request->format->toCsvFormat());
        } else {
            $html = $this->twig->render(self::PDF_TEMPLATE, [
                'title'  => $definition->getTitle(),
                'header' => $header,
                'rows'   => $rows,
            ]);
            $content = $this->exportService->getPdfFromHtml($html, count($columns) >= self::PDF_LANDSCAPE_MIN);
        }

        return new ExportResult($filename, $request->format->mimeType(), $content);
    }

    /**
     * @param list<string>|null $columnKeys
     *
     * @return list<ExportColumn>
     */
    private function selectColumns(ExportDefinitionInterface $definition, ?array $columnKeys): array
    {
        $all = $definition->getColumns();
        if (null === $columnKeys || [] === $columnKeys) {
            return array_values(array_filter($all, static fn (ExportColumn $c): bool => $c->defaultSelected));
        }
        /** @var array<string, ExportColumn> $byKey */
        $byKey = [];
        foreach ($all as $column) {
            $byKey[$column->key] = $column;
        }
        $selected = [];
        foreach ($columnKeys as $key) {
            if (isset($byKey[$key])) {
                $selected[] = $byKey[$key];
            }
        }
        if ([] === $selected) {
            return array_values(array_filter($all, static fn (ExportColumn $c): bool => $c->defaultSelected));
        }

        return $selected;
    }

    private function formatValue(mixed $value, string $type): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format(ExportColumn::TYPE_DATE === $type ? 'Y-m-d' : 'Y-m-d H:i');
        }
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

    private function buildFilename(ExportDefinitionInterface $definition, ExportRequest $request): string
    {
        return $definition->getKey().'_'.date('Y-m-d_His').'.'.$request->format->fileExtension();
    }
}
