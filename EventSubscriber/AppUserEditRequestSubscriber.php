<?php

/**
 * @noinspection MissingParameterTypeDeclarationInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEditRequest;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Exceptions\NotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotUniqueException;
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
     * @param ViewEvent $event
     *
     * @throws UserNotFoundException
     * @throws UserNotUniqueException
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
     * @throws NotImplementedException
     * @throws OswisException
     * @throws InvalidTypeException
     * @throws NotFoundException
     */
    public function sendMail(ViewEvent $event): void
    {
        if ($userEditRequest = $this->extractUserEditRequest($event)) {
            $this->editRequestService->sendMail($userEditRequest);
        }
    }
}
