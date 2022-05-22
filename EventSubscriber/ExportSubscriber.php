<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\EventSubscriber;

use Doctrine\Common\Collections\Collection;
use Mpdf\MpdfException;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ExportSubscriber extends AbstractExportSubscriber
{
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
     * @param  ViewEvent  $event
     * @param  \Doctrine\Common\Collections\Collection  $items
     *
     * @throws \Mpdf\MpdfException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function exportPdfList(ViewEvent $event, Collection $items): void
    {
        $data = [
            'items' => $items,
        ];
        $event->setResponse($this->getExportResponse('oswis-pdf-export.pdf', 'application/pdf',
            $this->encodeData($this->pdfGenerator->getPdfAsString(AppUser::getPdfListConfig(false, $data)))));
    }

    public function exportCsv(ViewEvent $event, Collection $items): void
    {
        $data = "\"ID\";\"Uživatelské jméno\";\"Celé jméno\";\n";
        foreach ($items as $item) {
            if ($item instanceof AppUser) {
                $data .= $item->getId().";";
                $data .= "\"".$item->getUsername()."\";";
                $data .= "\"".$item->getName()."\";\n";
            }
        }
        $event->setResponse($this->getExportResponse('oswis-export.csv', 'text/csv', $data));
    }
}
