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
        $pdf = $this->getMPdf($pdfList)->Output('', 'S');

        return is_string($pdf) ? $pdf : '';
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

    /**
     * Vyrenderuje libovolné HTML do PDF (string). Pro generický export framework.
     *
     * @throws MpdfException
     */
    public function getPdfFromHtml(string $html, bool $landscape = false): string
    {
        $app = $this->oswisCoreSettings->getApp();
        $appName = is_string($app['name'] ?? null) ? $app['name'] : '';
        $mPdf = new Mpdf([
            'format'        => 'A4'.($landscape ? '-L' : ''),
            'mode'          => 'utf-8',
            'margin_top'    => 14,
            'margin_bottom' => 14,
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_footer' => 6,
        ]);
        $mPdf->setLogger($this->logger);
        $mPdf->SetAuthor($appName);
        $mPdf->SetCreator($this->oswisCoreSettings->getCoreAppName());
        $mPdf->useSubstitutions = true;
        $mPdf->showImageErrors = true;
        // Brandovaná patička: appka + datum generování vlevo, číslo stránky vpravo.
        $mPdf->SetHTMLFooter(
            '<table width="100%" style="font-family:sans-serif; font-size:7pt; color:#888; border-top:0.5px solid #ccc; padding-top:2px;"><tr>'
            .'<td>'.htmlspecialchars($appName).' · vygenerováno '.date('j. n. Y H:i').'</td>'
            .'<td align="right">strana {PAGENO} / {nbpg}</td>'
            .'</tr></table>'
        );
        $mPdf->WriteHTML($html);
        $output = $mPdf->Output('', 'S');

        return is_string($output) ? $output : '';
    }
}
