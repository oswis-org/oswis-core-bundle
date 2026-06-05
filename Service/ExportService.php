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
        protected OswisCoreSettingsProvider $oswisCoreSettings,
        protected string $projectDir = ''
    ) {
    }

    /**
     * Resolve the configured app logo (oswis.app.logo, a project-relative path) to an absolute
     * filesystem path mPDF can embed. Tries the public web root first, then the project root.
     * Returns '' when not configured or the file is missing (header then degrades to text only).
     */
    private function resolveLogoPath(): string
    {
        $app = $this->oswisCoreSettings->getApp();
        $logoRel = is_string($app['logo'] ?? null) ? trim($app['logo']) : '';
        if ('' === $logoRel || '' === $this->projectDir) {
            return '';
        }
        foreach (['/public/', '/'] as $prefix) {
            $candidate = $this->projectDir.$prefix.ltrim($logoRel, '/');
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return '';
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
     * @param string|null       $title    PDF metadata: titulek dokumentu (viewer / vyhledávání)
     * @param string|null       $subject  PDF metadata: předmět (scope dokumentu)
     * @param list<string>      $keywords PDF metadata: klíčová slova
     *
     * @throws MpdfException
     */
    public function getPdfFromHtml(string $html, bool $landscape = false, ?string $title = null, ?string $subject = null, array $keywords = []): string
    {
        $app = $this->oswisCoreSettings->getApp();
        $appName = is_string($app['name'] ?? null) ? $app['name'] : '';
        $creator = $this->oswisCoreSettings->getCoreAppName();
        $mPdf = new Mpdf([
            'format'        => 'A4'.($landscape ? '-L' : ''),
            'mode'          => 'utf-8',
            'margin_top'    => 24, // room for the branded (logo) running header
            'margin_bottom' => 14,
            'margin_left'   => 10,
            'margin_right'  => 10,
            'margin_header' => 8,
            'margin_footer' => 6,
        ]);
        $mPdf->setLogger($this->logger);
        // Plná sada PDF metadat (Producer/CreationDate/ModDate doplní mPDF automaticky).
        $mPdf->SetAuthor($appName);
        $mPdf->SetCreator($creator);
        if (null !== $title && '' !== $title) {
            $mPdf->SetTitle($title);
        }
        $mPdf->SetSubject($subject ?? ($title ?? ''));
        $kw = array_values(array_filter([$appName, ...$keywords]));
        if ([] !== $kw) {
            $mPdf->SetKeywords(implode(', ', $kw));
        }
        $mPdf->SetDisplayMode('fullpage');
        // Outline/záložky: H1 (titulek dokumentu) → záložka — navigace ve vícestránkových PDF.
        $mPdf->h2bookmarks = ['H1' => 0];
        $mPdf->useSubstitutions = true;
        $mPdf->showImageErrors = true;
        // Robustnost velkých exportů: u rozsáhlého HTML (stovky+ řádků) hrozí
        // "HTML code size is larger than pcre.backtrack_limit" → navýšit limit dle délky HTML.
        $needed = strlen($html) * 2 + 200000;
        if ((int) ini_get('pcre.backtrack_limit') < $needed) {
            ini_set('pcre.backtrack_limit', (string) $needed);
        }
        if ((int) ini_get('pcre.recursion_limit') < $needed) {
            ini_set('pcre.recursion_limit', (string) $needed);
        }
        // Brandovaná opakující se hlavička: logo vlevo, název appky vpravo (na každé stránce).
        $logoPath = $this->resolveLogoPath();
        $logoTag = '' !== $logoPath ? '<img src="'.htmlspecialchars($logoPath).'" height="30">' : '';
        $mPdf->SetHTMLHeader(
            '<table width="100%" style="border-bottom:0.5px solid #006FAD; padding-bottom:3px;"><tr>'
            .'<td style="vertical-align:middle;">'.$logoTag.'</td>'
            .'<td align="right" style="vertical-align:middle; font-family:sans-serif; font-size:8pt; color:#006FAD; font-weight:bold;">'.htmlspecialchars($appName).'</td>'
            .'</tr></table>'
        );
        // Brandovaná patička: datum generování vlevo, číslo stránky vpravo.
        $mPdf->SetHTMLFooter(
            '<table width="100%" style="font-family:sans-serif; font-size:7pt; color:#888; border-top:0.5px solid #ccc; padding-top:2px;"><tr>'
            .'<td>vygenerováno '.date('j. n. Y H:i').'</td>'
            .'<td align="right">strana {PAGENO} / {nbpg}</td>'
            .'</tr></table>'
        );
        $mPdf->WriteHTML($html);
        $output = $mPdf->Output('', 'S');

        return is_string($output) ? $output : '';
    }
}
