<?php
/**
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Exceptions\OswisException;
use Zakjakub\OswisCoreBundle\Service\AppUserService;

final class AppUserSubscriber implements EventSubscriberInterface
{
    private AppUserService $appUserService;

    public function __construct(AppUserService $appUserService)
    {
        $this->appUserService = $appUserService;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::VIEW => ['makeAppUser', EventPriorities::POST_WRITE]];
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
        } catch (Exception $e) {
            return;
        }
        if (Request::METHOD_POST !== $method) {
            return;
        }
        $this->appUserService->appUserAction($appUser, AppUserService::ACTIVATION_REQUEST);
    }
}
