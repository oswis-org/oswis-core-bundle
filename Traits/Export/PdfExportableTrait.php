<?php
/**
 * @noinspection PhpUnusedParameterInspection
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Export;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\PdfList;

trait PdfExportableTrait
{
    public static function getPdfListConfig(bool $complex = false): PdfList
    {
        return new PdfList(self::getExportEntityName(11), self::getPdfListColumns($complex));
    }

    public static function getExportEntityName(int $case = 1): string
    {
        return 1 === $case ? 'Export položky' : 'Export položek';
    }

    public static function getPdfListColumns(bool $complex = false): Collection
    {
        return new ArrayCollection();
    }
}
