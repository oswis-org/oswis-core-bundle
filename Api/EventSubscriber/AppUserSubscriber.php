<?php

namespace Zakjakub\OswisCoreBundle\Api\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Manager\AppUserManager;
use function assert;
use function in_array;

/** @noinspection ClassNameCollisionInspection */

final class AppUserSubscriber implements EventSubscriberInterface
{
    private $appUserManager;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        EntityManagerInterface $em,
        AppUserManager $appUserManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->appUserManager = $appUserManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['appUserAction', EventPriorities::POST_VALIDATE],
        ];
    }

    /**
     * @param ViewEvent $event
     *
     * @throws ErrorException
     * @throws InvalidArgumentException
     * @throws NotFoundHttpException
     * @throws TransportExceptionInterface
     */
    public function appUserAction(ViewEvent $event): void
    {
        $out = null;
        $request = $event->getRequest();

        if ('api_app_user_action_requests_post_collection' !== $request->attributes->get('_route')) {
            return;
        }

        $controllerResult = $event->getControllerResult();
        $uid = $controllerResult->uid;
        $type = $controllerResult->type;
        $token = $controllerResult->token;
        $password = $controllerResult->password;
        $appUser = $controllerResult->appUser;

        $em = $this->em;
        $appUserRepository = $em->getRepository(AppUser::class);
        $appUser = $appUser ?? $appUserRepository->findOneBy(['id' => $uid]);

        if (!$appUser) {
            throw new NotFoundHttpException('Uživatel nenalezen.');
        }
        assert($appUser instanceof AppUser);

        if (in_array($type, AppUserManager::ALLOWED_TYPES, true)) {
            $this->appUserManager->appUserAction($appUser, $type, $password, $token);
        } else {
            throw new InvalidArgumentException('Akce "'.$type.'" není u uživatelských účtů implementována.');
        }

        $data = [];
        $event->setResponse(new JsonResponse($data, 201));
    }

}
