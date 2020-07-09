<?php
/**
 * @noinspection UnusedConstructorDependenciesInspection
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mpdf\MpdfException;
use OswisOrg\OswisCoreBundle\Service\ExportService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class AbstractExportSubscriber implements EventSubscriberInterface
{
    protected ExportService $pdfGenerator;

    public function __construct(ExportService $pdfGenerator)
    {
        $this->pdfGenerator = $pdfGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['export', EventPriorities::PRE_RESPOND,],
            ],
        ];
    }

    public function getExportResponse(string $fileName, string $mimeType, string $data, int $code = Response::HTTP_OK): Response
    {
        return new JsonResponse(
            [
                'fileName' => $fileName,
                'mimeType' => $mimeType,
                'data'     => $this->encodeData($data),
            ], $code
        );
    }

    public function encodeData(string $data): string
    {
        return chunk_split(base64_encode($data));
    }

    /**
     * @param ViewEvent $viewEvent
     *
     * @throws LoaderError
     * @throws MpdfException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    abstract public function export(ViewEvent $viewEvent): void;

    /**
     * @param mixed $result
     *
     * @return Collection
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    public function getCollectionFromResult($result): Collection
    {
        if (is_array($result)) {
            return new ArrayCollection($result);
        }
        if ($result instanceof Collection) {
            return $result;
        }

        return new ArrayCollection();
    }
}
