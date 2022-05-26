<?php

/**
 * @noinspection MissingParameterTypeDeclarationInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEdit;
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
     * @param  \Symfony\Component\HttpKernel\Event\ViewEvent  $event
     *
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\TokenInvalidException
     */
    public function process(ViewEvent $event): void
    {
        if ($userEdit = $this->extractUserEdit($event)) {
            $this->editService->assignRequest($userEdit);
            $userEdit->process();
        }
    }

    /**
     * @param  \Symfony\Component\HttpKernel\Event\ViewEvent  $event
     *
     * @return \OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEdit|null
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
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\OswisException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\NotFoundException
     */
    public function sendConfirmation(ViewEvent $event): void
    {
        if ($userEdit = $this->extractUserEdit($event)) {
            $this->editService->sendConfirmation($userEdit);
        }
    }
}

