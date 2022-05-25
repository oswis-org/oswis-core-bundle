<?php

/**
 * @noinspection MissingParameterTypeDeclarationInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEditRequest;
use OswisOrg\OswisCoreBundle\Service\AppUserEditRequestService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class AppUserEditRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AppUserEditRequestService $editRequestService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['assignUser', EventPriorities::PRE_WRITE,],
                ['sendMail', EventPriorities::POST_WRITE,],
            ],
        ];
    }

    /**
     * @param  \Symfony\Component\HttpKernel\Event\ViewEvent  $event
     *
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\UserNotFoundException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\UserNotUniqueException
     */
    public function assignUser(ViewEvent $event): void
    {
        if ($userEditRequest = $this->extractUserEditRequest($event)) {
            $this->editRequestService->assignAppUser($userEditRequest);
        }
    }

    private function extractUserEditRequest(ViewEvent $event): ?AppUserEditRequest
    {
        try {
            $userEditRequest = $event->getControllerResult();
            if (!($userEditRequest instanceof AppUserEditRequest)) {
                return null;
            }
            $method = $event->getRequest()->getMethod();
            if (Request::METHOD_POST !== $method) {
                return null;
            }
        } catch (SuspiciousOperationException) {
            return null;
        }

        return $userEditRequest;
    }

    /**
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\OswisException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\NotFoundException
     */
    public function sendMail(ViewEvent $event): void
    {
        if ($userEditRequest = $this->extractUserEditRequest($event)) {
            $this->editRequestService->sendMail($userEditRequest);
        }
    }
}
