<?php

namespace Zakjakub\OswisCoreBundle\Service;

use DateTime;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Zakjakub\OswisCoreBundle\Provider\OswisCoreSettingsProvider;

/**
 * Service for generating pdf documents.
 */
class PdfGenerator
{
    public const DEFAULT_TEMPLATE = '@ZakjakubOswisCore/documents/pdf-document.html.twig';
    public const DEFAULT_HEADER_TEMPLATE = '@ZakjakubOswisCore/documents/parts/header.html.twig';
    public const DEFAULT_FOOTER_TEMPLATE = '@ZakjakubOswisCore/documents/parts/footer.html.twig';
    public const DEFAULT_PAPER_FORMAT = 'A4';
    public const DEFAULT_PAPER_LANDSCAPE = false;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Environment
     */
    protected $templating;

    /**
     * @var OswisCoreSettingsProvider
     */
    protected $oswisCoreSettings;

    /**
     * PDF generator constructor.
     *
     * @param LoggerInterface           $logger
     * @param Environment               $templating
     * @param OswisCoreSettingsProvider $oswisCoreSettings
     */
    public function __construct(
        LoggerInterface $logger,
        Environment $templating,
        OswisCoreSettingsProvider $oswisCoreSettings
    ) {
        $this->logger = $logger;
        $this->templating = $templating;
        $this->oswisCoreSettings = $oswisCoreSettings;
    }

    /** @noinspection PhpUnused */
    /**
     * @param string|null $title
     * @param string|null $template
     * @param array|null  $data
     * @param string      $format
     * @param bool        $landscape
     * @param string|null $headerTemplate
     * @param string|null $footerTemplate
     *
     * @return string
     * @throws LoaderError
     * @throws MpdfException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function generatePdfAsString(
        ?string $title,
        string $template = self::DEFAULT_TEMPLATE,
        array $data = [],
        string $format = self::DEFAULT_PAPER_FORMAT,
        bool $landscape = self::DEFAULT_PAPER_LANDSCAPE,
        string $headerTemplate = self::DEFAULT_HEADER_TEMPLATE,
        string $footerTemplate = self::DEFAULT_FOOTER_TEMPLATE
    ): string {
        $format .= $landscape ? '-L' : null;
        $context = array(
            'title'    => $title,
            'dateTime' => new DateTime(),
            'oswis'    => $this->oswisCoreSettings,
            'data'     => $data,
        );
        $mPdf = new Mpdf(['format' => $format, 'mode' => 'utf-8', 'logger' => $this->logger]);
        $mPdf->SetTitle($title);
        $mPdf->SetSubject($title);
        $mPdf->SetAuthor($this->oswisCoreSettings->getApp()['name']);
        $mPdf->SetCreator($this->oswisCoreSettings->getCoreAppName());
        $mPdf->h2toc = array('H1' => 0, 'H2' => 1, 'H3' => 2, 'H4' => 3, 'H5' => 4, 'H6' => 5);
        $mPdf->showImageErrors = true;
        $mPdf->useSubstitutions = true;
        $mPdf->SetHTMLHeader($this->templating->render($headerTemplate, $context));
        $mPdf->SetHTMLFooter($this->templating->render($footerTemplate, $context));
        $mPdf->WriteHTML($this->templating->render($template, $context));

        return $mPdf->Output('', 'S');
    }
}
