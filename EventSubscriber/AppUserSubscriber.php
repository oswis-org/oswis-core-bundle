<?php

/**
 * @noinspection MissingParameterTypeDeclarationInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Service\AppUserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class AppUserSubscriber implements EventSubscriberInterface
{
    private AppUserService $appUserService;

    public function __construct(AppUserService $appUserService)
    {
        $this->appUserService = $appUserService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['makeAppUser', (int)EventPriorities::POST_WRITE,],
            ],
        ];
    }

    /**
     * @param  ViewEvent  $event
     *
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\NotFoundException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\OswisException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\TokenInvalidException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\UserNotFoundException
     */
    public function makeAppUser(ViewEvent $event): void
    {
        $appUser = $event->getControllerResult();
        if (!($appUser instanceof AppUser)) {
            return;
        }
        try {
            $method = $event->getRequest()->getMethod();
        } catch (SuspiciousOperationException) {
            return;
        }
        if (Request::METHOD_POST !== $method) {
            return;
        }
        $this->appUserService->requestActivation($appUser);
    }
}
