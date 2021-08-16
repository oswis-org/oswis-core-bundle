<?php

/**
 * @noinspection PhpUnusedParameterInspection
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Export;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Export\PdfExportList;

trait PdfExportableTrait
{
    public static function getPdfListConfig(bool $complex = false, array $data = null): PdfExportList
    {
        return new PdfExportList(self::getExportEntityName(11), self::getPdfListColumns($complex), $data);
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
