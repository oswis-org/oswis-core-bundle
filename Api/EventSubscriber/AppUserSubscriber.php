<?php

namespace Zakjakub\OswisCoreBundle\Api\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Manager\AppUserManager;

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
     * @param GetResponseForControllerResultEvent $event
     *
     * @throws NotFoundHttpException
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function appUserAction(GetResponseForControllerResultEvent $event): void
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
        \assert($em instanceof EntityManagerInterface);
        $appUserRepository = $em->getRepository(AppUser::class);

        if (!$appUser) {
            $appUser = $appUserRepository->findOneBy(['id'=>$uid]);
        }

        if (!$appUser) {
            throw new NotFoundHttpException('Uživatel nenalezen.');
        }

        \assert($appUser instanceof AppUser);
        if ($type === 'change-password') {
            if (!$appUser || !$token || !$password) {
                throw new \InvalidArgumentException('Chybějící položky v požadavku pro změnu hesla.');
            }
            $out = $this->appUserManager->changePassword($appUser, true, $password);
        } elseif ($type === 'activation') {
            if (!$appUser || !$token || !$password) {
                throw new \InvalidArgumentException('Chybějící položky v požadavku na aktivaci.');
            }
            $out = $this->appUserManager->activateAccount($appUser, true, $password, $token);
        }

        $data = ['data' => chunk_split(base64_encode($out))];

        $event->setResponse(new JsonResponse($data, 201));
    }


    /**
     * @return AppUser
     * @throws \Exception
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
            throw new AccessDeniedException('Neznámý uživatel.');
        }
        $accommodationUserRepo = $this->em->getRepository(AccommodationUser::class);
        $accommodationUser = $accommodationUserRepo->findOneBy(['appUser' => $appUser->getId()]);
        \assert($accommodationUser instanceof AccommodationUser);
        if (!$accommodationUser) {
            throw new AccessDeniedException('Neznámý uživatel ubytovacího systému.');
        }

        return $accommodationUser;
    }

}
