<?php

/**
 * @noinspection MissingParameterTypeDeclarationInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEdit;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Exceptions\NotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Exceptions\TokenInvalidException;
use OswisOrg\OswisCoreBundle\Service\AppUserEditService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class AppUserEditSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AppUserEditService $editService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['process', EventPriorities::PRE_WRITE,],
                ['sendConfirmation', EventPriorities::POST_WRITE,],
            ],
        ];
    }

    /**
     * @param ViewEvent $event
     *
     * @throws TokenInvalidException
     * @throws OswisException
     */
    public function process(ViewEvent $event): void
    {
        if ($userEdit = $this->extractUserEdit($event)) {
            $this->editService->assignRequest($userEdit);
            $userEdit->process();
        }
    }

    /**
     * @param ViewEvent $event
     *
     * @return AppUserEdit|null
     */
    private function extractUserEdit(ViewEvent $event): ?AppUserEdit
    {
        try {
            $userEdit = $event->getControllerResult();
            if (!($userEdit instanceof AppUserEdit)) {
                return null;
            }
            $method = $event->getRequest()->getMethod();
            if (Request::METHOD_POST !== $method) {
                return null;
            }
        } catch (SuspiciousOperationException) {
            return null;
        }

        return $userEdit;
    }

    /**
     * @throws NotImplementedException
     * @throws OswisException
     * @throws InvalidTypeException
     * @throws NotFoundException
     */
    public function sendConfirmation(ViewEvent $event): void
    {
        if ($userEdit = $this->extractUserEdit($event)) {
            $this->editService->sendConfirmation($userEdit);
        }
    }
}

