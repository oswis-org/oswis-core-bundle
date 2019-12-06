<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Api\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Exceptions\OswisException;
use Zakjakub\OswisCoreBundle\Exceptions\OswisNotImplementedException;
use Zakjakub\OswisCoreBundle\Exceptions\OswisUserNotFoundException;
use Zakjakub\OswisCoreBundle\Exceptions\OswisUserNotUniqueException;
use Zakjakub\OswisCoreBundle\Manager\AppUserManager;
use Zakjakub\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use function assert;
use function in_array;

/**
 * Handler for endpoint for actions with users (activation, password changes...).
 */
final class AppUserActionSubscriber implements EventSubscriberInterface
{
    /**
     * @var AppUserManager
     */
    private AppUserManager $appUserManager;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * AppUserActionSubscriber constructor.
     */
    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder,
        LoggerInterface $logger,
        MailerInterface $mailer,
        OswisCoreSettingsProvider $oswisCoreSettings
    ) {
        $this->em = $em;
        $this->appUserManager = new AppUserManager($encoder, $em, $logger, $mailer, $oswisCoreSettings);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['appUserAction', EventPriorities::POST_VALIDATE],
        ];
    }

    /**
     * @throws OswisException
     * @throws OswisNotImplementedException
     * @throws OswisUserNotFoundException
     */
    public function appUserAction(ViewEvent $event): void
    {
        $em = $this->em;
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
        try {
            $appUserRepository = $em->getRepository(AppUser::class);
            $appUser = $appUser ?? $appUserRepository->loadUserById($uid);
            $appUser = $appUser ?? $appUserRepository->loadUserByUsername($username);
            if (!$appUser && $token) {
                $appUserByToken = $this->em->getRepository(AppUser::class)->findOneBy(['passwordResetRequestToken' => $token]);
                $appUser = $appUserByToken && $appUserByToken->checkPasswordResetRequestToken($token) ? $appUserByToken : null;
            }
            if (!$appUser && $token) {
                $appUserByToken = $this->em->getRepository(AppUser::class)->findOneBy(['accountActivationRequestToken' => $token]);
                $appUser = $appUserByToken && $appUserByToken->checkAccountActivationRequestToken($token) ? $appUserByToken : null;
            }
        } catch (OswisUserNotUniqueException $e) {
            $appUser = null;
        }
        if (!$appUser) {
            throw new OswisUserNotFoundException();
        }
        assert($appUser instanceof AppUser);
        if (in_array($type, AppUserManager::ALLOWED_TYPES, true)) {
            $this->appUserManager->appUserAction($appUser, $type, $password, $token);
        } else {
            throw new OswisNotImplementedException($type, 'u uživatelských účtů');
        }
        $data = [];
        $event->setResponse(new JsonResponse($data, 201));
    }
}
