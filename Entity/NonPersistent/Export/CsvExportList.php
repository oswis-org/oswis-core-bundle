<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent\Export;

/**
 * Definition for CSV export file.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
class CsvExportList extends AbstractExportList
{
    public const DEFAULT_TEMPLATE        = '@OswisOrgOswisCore/export/csv/document.pdf.html.twig';
    public const DEFAULT_HEADER_TEMPLATE = '@OswisOrgOswisCore/export/csv/header.pdf.html.twig';
    public const DEFAULT_FOOTER_TEMPLATE = '@OswisOrgOswisCore/export/csv/footer.pdf.html.twig';
}
