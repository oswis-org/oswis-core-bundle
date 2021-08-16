<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Service;

use Mpdf\Mpdf;
use Mpdf\MpdfException;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Export\PdfExportList;
use OswisOrg\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Service for generating pdf export.
 */
class ExportService
{
    public function __construct(
        protected LoggerInterface $logger,
        protected Environment $templating,
        protected OswisCoreSettingsProvider $oswisCoreSettings
    ) {
    }

    /**
     * @throws LoaderError
     * @throws MpdfException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function getPdfAsString(PdfExportList $pdfList): string
    {
        return $this->getMPdf($pdfList)->Output('', 'S');
    }

    /**
     * @param  PdfExportList  $pdfList
     *
     * @return Mpdf
     * @throws LoaderError
     * @throws MpdfException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getMPdf(PdfExportList $pdfList): Mpdf
    {
        $mPdf = $pdfList->getMPdf();
        $mPdf->setLogger($this->logger);
        $mPdf->SetAuthor($this->oswisCoreSettings->getApp()['name']);
        $mPdf->SetCreator($this->oswisCoreSettings->getCoreAppName());
        if ($pdfList->getHeaderTemplate()) {
            $mPdf->SetHTMLHeader($this->templating->render(''.$pdfList->getHeaderTemplate(), $pdfList->getContext()));
        }
        if ($pdfList->getFooterTemplate()) {
            $mPdf->SetHTMLFooter($this->templating->render(''.$pdfList->getFooterTemplate(), $pdfList->getContext()));
        }
        $mPdf->WriteHTML($this->templating->render(''.$pdfList->getTemplate(), $pdfList->getContext()));

        return $mPdf;
    }
}
