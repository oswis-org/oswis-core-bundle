<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Api\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Exceptions\OswisException;
use Zakjakub\OswisCoreBundle\Exceptions\OswisNotImplementedException;
use Zakjakub\OswisCoreBundle\Exceptions\OswisUserNotFoundException;
use Zakjakub\OswisCoreBundle\Exceptions\OswisUserNotUniqueException;
use Zakjakub\OswisCoreBundle\Service\AppUserService;
use function assert;
use function in_array;

/**
 * Handler for endpoint for actions with users (activation, password changes...).
 */
final class AppUserActionSubscriber implements EventSubscriberInterface
{
    private AppUserService $appUserService;

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, AppUserService $appUserService)
    {
        $this->em = $em;
        $this->appUserService = $appUserService;
    }

    /**
     * @return array<string, array<int, int|string>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['appUserAction', EventPriorities::POST_VALIDATE],
        ];
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
        $out = null;
        $request = $event->getRequest();
        if ('api_app_user_action_requests_post_collection' !== $request->attributes->get('_route')) {
            return;
        }
        $controllerResult = $event->getControllerResult();
        // TODO: Refactor to array ($data[]).
        $uid = $controllerResult->uid;
        $username = $controllerResult->username;
        $type = $controllerResult->type;
        $token = $controllerResult->token;
        $password = $controllerResult->password;
        $appUser = $controllerResult->appUser;
        $appUserRepository = $this->appUserService->getRepository();
        $appUser ??= $appUserRepository->loadUserById($uid) ?? $appUserRepository->loadUserByUsername($username);
        if (!$appUser && $token) {
            $appUserByToken = $this->em->getRepository(AppUser::class)->findOneBy(['passwordResetRequestToken' => $token]);
            $appUser = $appUserByToken && $appUserByToken->checkPasswordResetRequestToken($token) ? $appUserByToken : null;
        }
        if (!$appUser && $token) {
            $appUserByToken = $this->em->getRepository(AppUser::class)->findOneBy(['accountActivationRequestToken' => $token]);
            $appUser = $appUserByToken && $appUserByToken->checkAccountActivationRequestToken($token) ? $appUserByToken : null;
        }
        if (!$appUser) {
            throw new OswisUserNotFoundException();
        }
        assert($appUser instanceof AppUser);
        if (in_array($type, AppUserService::ALLOWED_TYPES, true)) {
            $this->appUserService->appUserAction($appUser, $type, $password, $token);
        } else {
            throw new OswisNotImplementedException($type, 'u uživatelských účtů');
        }
        $data = [];
        $event->setResponse(new JsonResponse($data, 201));
    }
}
