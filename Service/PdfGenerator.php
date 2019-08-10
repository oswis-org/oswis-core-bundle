<?php /** @noinspection UnknownInspectionInspection */

namespace Zakjakub\OswisCoreBundle\Service;

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

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Twig_Environment
     */
    protected $templating;

    /**
     * @var OswisCoreSettingsProvider
     */
    protected $oswisCoreSettings;

    /**
     * E-mail sender constructor.
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


    /**
     * @param string|null $title
     * @param string|null $template
     * @param array|null  $data
     * @param string      $format
     * @param bool        $landscape
     *
     * @param string|null $header
     * @param string|null $footer
     *
     * @return string
     * @throws LoaderError
     * @throws MpdfException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function generatePdfAsString(
        ?string $title,
        ?string $template = '@ZakjakubOswisCore/documents/pdf-document.html.twig',
        array $data = [],
        string $format = 'A4',
        bool $landscape = false,
        string $header = null,
        string $footer = null
    ): string {
        $generatedDateText = date('j. n. Y G:i:s');
        if ($landscape) {
            $format .= '-L';
        }

        $header = $header ?? '';
        /** @noinspection HtmlDeprecatedAttribute */
        $footer = $footer ?? '
            <table width="100%" style="width:100%;">
                <tr>
                    <td width="33%" style="width:33%;">{DATE j.n.Y}</td>
                    <td width="33%" style="width:33%;" align="center" style="font-size: small;">'.$title.'</td>
                    <td width="33%" style="width:33%;" style="text-align: right;">{PAGENO}/{nbpg}</td>
                </tr>
            </table>
            ';

        $mPdf = new Mpdf(['format' => $format, 'mode' => 'utf-8']);
        $mPdf->SetTitle($title);
        $mPdf->SetSubject($title);
        $mPdf->SetAuthor($this->oswisCoreSettings->getApp()['name']);
        $mPdf->SetCreator($this->oswisCoreSettings->getCoreAppName());
        $mPdf->showImageErrors = true;

        $content = $this->templating->render(
            $template,
            array(
                'title'             => $title,
                'generatedDateText' => $generatedDateText,
                'data'              => $data,
            )
        );

        $mPdf->SetHTMLHeader($header);
        $mPdf->SetHTMLFooter($footer);
        $mPdf->WriteHTML($content);

        return $mPdf->Output('', 'S');
    }
}
