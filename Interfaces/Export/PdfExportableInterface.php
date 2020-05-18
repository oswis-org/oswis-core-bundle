<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Interfaces\Export;

use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\PdfList;

interface PdfExportableInterface
{
    public static function getExportEntityName(int $case = 1): string;

    public static function getPdfListConfig(bool $complex = false): PdfList;

    public static function getPdfListColumns(bool $complex = false): Collection;
}
