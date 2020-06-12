<?php /** @noinspection MissingParameterTypeDeclarationInspection */

/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotFoundException;
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
                ['makeAppUser', EventPriorities::POST_WRITE,],
            ],
        ];
    }

    /**
     * @param ViewEvent $event
     *
     * @throws NotImplementedException|UserNotFoundException|InvalidTypeException|OswisException
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
