<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Service\AppUserService;
use OswisOrg\OswisCoreBundle\Service\PdfGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class AppUserSubscriber implements EventSubscriberInterface
{
    private AppUserService $appUserService;

    private PdfGenerator $pdfGenerator;

    public function __construct(AppUserService $appUserService, PdfGenerator $pdfGenerator)
    {
        $this->appUserService = $appUserService;
        $this->pdfGenerator = $pdfGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['makeAppUser', EventPriorities::POST_WRITE,],
                ['exportCsv', EventPriorities::PRE_RESPOND,],
            ],
        ];
    }

    public function exportCsv(ViewEvent $event): void
    {
        $request = $event->getRequest();
        if ('api_app_users_csv_collection' !== $request->attributes->get('_route')) {
            return;
        }
        $this->export($event);
    }

    public function exportPdf(ViewEvent $event): void
    {
        $request = $event->getRequest();
        if ('api_app_users_pdf_collection' !== $request->attributes->get('_route')) {
            return;
        }
        $this->export($event);
    }

    public function export(ViewEvent $event): void
    {
        // $items = $event->getControllerResult();
        $event->setResponse(new JsonResponse(['data' => chunk_split(base64_encode('foo'))], Response::HTTP_OK));
    }

    /**
     * @throws OswisException
     */
    public function makeAppUser(ViewEvent $event): void
    {
        $appUser = $event->getControllerResult();
        if (!($appUser instanceof AppUser)) {
            return;
        }
        try {
            $method = $event->getRequest()->getMethod();
        } catch (SuspiciousOperationException $e) {
            return;
        }
        if (Request::METHOD_POST !== $method) {
            return;
        }
        $this->appUserService->appUserAction($appUser, AppUserService::ACTIVATION_REQUEST);
    }
}
