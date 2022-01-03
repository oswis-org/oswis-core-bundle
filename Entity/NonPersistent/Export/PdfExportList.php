<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent\Export;

use Mpdf\Mpdf;
use Mpdf\MpdfException;

/**
 * Definition for PDF export file.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
class PdfExportList extends AbstractExportList
{
    public const DEFAULT_TEMPLATE = '@OswisOrgOswisCore/export/pdf/document.pdf.html.twig';
    public const DEFAULT_HEADER_TEMPLATE = '@OswisOrgOswisCore/export/pdf/header.pdf.html.twig';
    public const DEFAULT_FOOTER_TEMPLATE = '@OswisOrgOswisCore/export/pdf/footer.pdf.html.twig';
    public const DEFAULT_PAPER_FORMAT = 'A4';
    public const DEFAULT_PAPER_LANDSCAPE = false;
    public const DEFAULT_TITLE = 'Export';
    public const DEFAULT_DATA = ['items' => null];

    public string $format = self::DEFAULT_PAPER_FORMAT;

    public bool $landscape = self::DEFAULT_PAPER_LANDSCAPE;

    /**
     * @return Mpdf
     * @throws MpdfException
     */
    public function getMPdf(): Mpdf
    {
        $mPdf = new Mpdf(['format' => $this->getFormatString(), 'mode' => 'utf-8']);
        $mPdf->SetTitle($this->getTitle());
        $mPdf->SetSubject($this->getTitle());
        $mPdf->h2toc = ['H1' => 0, 'H2' => 1, 'H3' => 2, 'H4' => 3, 'H5' => 4, 'H6' => 5];
        $mPdf->showImageErrors = true;
        $mPdf->useSubstitutions = true;

        return $mPdf;
    }

    public function getFormatString(): string
    {
        return $this->getFormat().($this->isLandscape() ? '-L' : '');
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function isLandscape(): bool
    {
        return $this->landscape;
    }

    public function setLandscape(bool $landscape): void
    {
        $this->landscape = $landscape;
    }

}
