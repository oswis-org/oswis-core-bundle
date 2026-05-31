<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\EventSubscriber;

use Doctrine\Common\Collections\Collection;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception as CsvException;
use Mpdf\MpdfException;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Enum\CsvFormat;
use OswisOrg\OswisCoreBundle\Service\CsvExportService;
use OswisOrg\OswisCoreBundle\Service\ExportService;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ExportSubscriber extends AbstractExportSubscriber
{
    public function __construct(ExportService $pdfGenerator, private readonly CsvExportService $csvExportService)
    {
        parent::__construct($pdfGenerator);
    }

    /**
     * @param  ViewEvent  $viewEvent
     *
     * @throws LoaderError
     * @throws MpdfException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function export(ViewEvent $viewEvent): void
    {
        $request = $viewEvent->getRequest();
        $result = $viewEvent->getControllerResult();
        if ('api_app_users_pdf_collection' === $request->attributes->get('_route')) {
            $this->exportPdfList($viewEvent, $this->getCollectionFromResult($result));
        }
        if ('api_app_users_csv_collection' === $request->attributes->get('_route')) {
            $this->exportCsv($viewEvent, $this->getCollectionFromResult($result));
        }
    }

    /**
     * @param  ViewEvent $event
     * @param Collection $items
     *
     * @throws MpdfException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function exportPdfList(ViewEvent $event, Collection $items): void
    {
        $data = [
            'items' => $items,
        ];
        $event->setResponse($this->getExportResponse('oswis-pdf-export.pdf', 'application/pdf',
            $this->encodeData($this->pdfGenerator->getPdfAsString(AppUser::getPdfListConfig(false, $data)))));
    }

    /**
     * @throws CsvException
     * @throws CannotInsertRecord
     */
    public function exportCsv(ViewEvent $event, Collection $items): void
    {
        $format = CsvFormat::fromRequest($event->getRequest()->query->getString('format'));
        $rows = [];
        foreach ($items as $item) {
            if ($item instanceof AppUser) {
                $rows[] = [$item->getId(), $item->getUsername(), $item->getName()];
            }
        }
        $csv = $this->csvExportService->build(['ID', 'Uživatelské jméno', 'Celé jméno'], $rows, $format);
        $event->setResponse($this->getExportResponse('oswis-export.csv', 'text/csv', $csv));
    }
}
