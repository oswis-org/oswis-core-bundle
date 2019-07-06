<?php

namespace Zakjakub\OswisCoreBundle\Api\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Exception;
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
        $this->appUserManager = $appUserManager;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
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
        assert($em instanceof EntityManagerInterface);
        $appUserRepository = $em->getRepository(AppUser::class);

        if (!$appUser) {
            $appUser = $appUserRepository->findOneBy(['id' => $uid]);
        }

        if (!$appUser) {
            throw new NotFoundHttpException('Uživatel nenalezen.');
        }
        assert($appUser instanceof AppUser);
        if ($type === 'reset') {
            $this->appUserManager->appUserAction($appUser, 'reset', $password, $token);
        } elseif ($type === 'activation') {
            $this->appUserManager->appUserAction($appUser, 'activation', $password, $token);
        } elseif ($type === 'reset-request') {
            $this->appUserManager->appUserAction($appUser, 'reset-request');
        } elseif ($type === 'activation-request') {
            $this->appUserManager->appUserAction($appUser, 'activation');
        } else {
            throw new InvalidArgumentException('Akce "'.$type.'" není u uživatelských účtů implementována.');
        }

        $data = [];
        $event->setResponse(new JsonResponse($data, 201));
    }


    /**
     * @return AppUser
     * @throws Exception
     */
    public function getCurrentAppUser(): AppUser
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return null;
        }
        $appUser = $token->getUser();
        if (!$appUser instanceof AppUser) {
            return null;
        }
        if (!$appUser) {
            return null;
        }
        $accommodationUserRepo = $this->em->getRepository(AccommodationUser::class);
        $accommodationUser = $accommodationUserRepo->findOneBy(['appUser' => $appUser->getId()]);
        assert($accommodationUser instanceof AccommodationUser);
        if (!$accommodationUser) {
            throw new AccessDeniedException('Neznámý uživatel ubytovacího systému.');
        }

        return $accommodationUser;
    }
}
