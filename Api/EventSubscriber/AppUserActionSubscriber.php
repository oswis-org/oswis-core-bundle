<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Api\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotUniqueException;
use OswisOrg\OswisCoreBundle\Service\AppUserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

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
     * @throws NotImplementedException
     * @throws UserNotFoundException
     * @throws UserNotUniqueException
     */
    public function appUserAction(ViewEvent $event): void
    {
        $request = $event->getRequest();
        if ('api_app_user_action_requests_post_collection' !== $request->attributes->get('_route')) {
            return;
        }
        $properties = $this->getRequestProperties($event->getControllerResult());
        if (null === ($appUser = $this->loadAppUser($properties)) || !($appUser instanceof AppUser)) {
            throw new UserNotFoundException();
        }
        $type = $properties['type'] ?? null;
        $status = $this->processAction($appUser, $properties['type'], $properties['password'], $properties['token']);
        if (null === $status) {
            throw new NotImplementedException($type, 'u uživatelských účtů');
        }
        $event->setResponse(new JsonResponse([], $status));
    }

    /**
     * @param AppUser     $appUser
     * @param string|null $type
     * @param string|null $password
     * @param string|null $token
     *
     * @return int|null
     * @throws InvalidTypeException
     * @throws OswisException
     * @throws UserNotFoundException
     */
    public function processAction(AppUser $appUser, ?string $type, ?string $password, ?string $token): ?int
    {
        if (AppUserService::PASSWORD_CHANGE_REQUEST === $type) {
            $this->appUserService->requestPasswordChange($appUser, true);

            return 201;
        }

        return null;
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
     * @throws UserNotUniqueException
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
