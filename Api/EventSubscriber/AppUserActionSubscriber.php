<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Api\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisNotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisUserNotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisUserNotUniqueException;
use OswisOrg\OswisCoreBundle\Service\AppUserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use function assert;
use function in_array;

/**
 * Handler for endpoint for actions with users (activation, password changes...).
 */
final class AppUserActionSubscriber implements EventSubscriberInterface
{
    private AppUserService $appUserService;

    public function __construct(AppUserService $appUserService)
    {
        $this->appUserService = $appUserService;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::VIEW => ['appUserAction', EventPriorities::POST_VALIDATE]];
    }

    /**
     * @param ViewEvent $event
     *
     * @throws OswisException
     * @throws OswisNotImplementedException
     * @throws OswisUserNotFoundException
     * @throws OswisUserNotUniqueException
     */
    public function appUserAction(ViewEvent $event): void
    {
        $request = $event->getRequest();
        if ('api_app_user_action_requests_post_collection' !== $request->attributes->get('_route')) {
            return;
        }
        $properties = $this->getRequestProperties($event->getControllerResult());
        // TODO: Refactor to array ($data[]).
        if (!($appUser = $this->loadAppUser($properties))) {
            throw new OswisUserNotFoundException();
        }
        assert($appUser instanceof AppUser);
        if (in_array($properties['type'], AppUserService::ALLOWED_TYPES, true)) {
            $this->appUserService->appUserAction($appUser, $properties['type'], $properties['password'], $properties['token']);
            $event->setResponse(new JsonResponse([], 201));

            return;
        }
        throw new OswisNotImplementedException($properties['type'], 'u uživatelských účtů');
    }

    /**
     * @param mixed $controllerResult
     *
     * @return array
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    public function getRequestProperties($controllerResult): array
    {
        return [
            'uid'      => $controllerResult->uid ?: null,
            'username' => $controllerResult->username ?: null,
            'token'    => $controllerResult->token ?: null,
            'appUser'  => $controllerResult->appUser ?: null,
            'type'     => $controllerResult->type ?: null,
            'password' => $controllerResult->password ?: null,
        ];
    }

    /**
     * @param array $properties
     *
     * @return AppUser|null
     * @throws OswisUserNotUniqueException
     */
    private function loadAppUser(array $properties): ?AppUser
    {
        $appUserRepository = $this->appUserService->getRepository();
        $appUser = $properties['appUser'];
        $appUser ??= $appUserRepository->loadUserById($properties['uid']) ?? $appUserRepository->loadUserByUsername($properties['username']);
        if (!$appUser && $properties['token']) {
            $appUserByToken = $this->appUserService->getRepository()->findOneBy(['passwordResetRequestToken' => $properties['token']]);
            $appUser = $appUserByToken && $appUserByToken->checkPasswordResetRequestToken($properties['token']) ? $appUserByToken : null;
        }
        if (!$appUser && $properties['token']) {
            $appUserByToken = $this->appUserService->getRepository()->findOneBy(['accountActivationRequestToken' => $properties['token']]);
            $appUser = $appUserByToken && $appUserByToken->checkActivationRequestToken($properties['token']) ? $appUserByToken : null;
        }

        return $appUser;
    }
}
