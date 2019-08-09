<?php

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

/** @noinspection ClassNameCollisionInspection */

/**
 * Handler for endpoint for actions with users (activation, password changes...).
 */
final class AppUserActionSubscriber implements EventSubscriberInterface
{
    /**
     * @var AppUserManager
     */
    private $appUserManager;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AppUserActionSubscriber constructor.
     *
     * @param EntityManagerInterface       $em
     * @param UserPasswordEncoderInterface $encoder
     * @param LoggerInterface              $logger
     * @param MailerInterface              $mailer
     * @param OswisCoreSettingsProvider    $oswisCoreSettings
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

    /**
     * @return array
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
     * @throws OswisNotImplementedException
     * @throws OswisUserNotFoundException
     * @throws OswisException
     */
    public function appUserAction(ViewEvent $event): void
    {
        $out = null;
        $request = $event->getRequest();

        if ('api_app_user_action_requests_post_collection' !== $request->attributes->get('_route')) {
            return;
        }

        $controllerResult = $event->getControllerResult();
        $uid = $controllerResult->uid ?? null;
        $username = $controllerResult->username ?? null;
        $type = $controllerResult->type ?? null;
        $token = $controllerResult->token ?? null;
        $password = $controllerResult->password ?? null;
        $appUser = $controllerResult->appUser ?? null;

        $em = $this->em;
        try {
            $appUserRepository = $em->getRepository(AppUser::class);
            $appUser = $appUser ?? $appUserRepository->loadUserById($uid);
            $appUser = $appUser ?? $appUserRepository->loadUserByUsername($username);
        } catch (OswisUserNotUniqueException $e) {
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
