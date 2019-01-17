<?php

namespace Zakjakub\OswisResourcesBundle\Api\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Zakjakub\OswisResourcesBundle\Manager\AppUserManager;

final class AppUserSubscriber implements EventSubscriberInterface
{
    private $appUserManager;

    private $container;

    public function __construct(
        AppUserManager $appUserManager,
        ContainerInterface $container
    ) {
        $this->appUserManager = $appUserManager;
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->container = $container;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['changePassword', EventPriorities::POST_VALIDATE],
        ];
    }

    public function changePassword(GetResponseForControllerResultEvent $event): void
    {
        $request = $event->getRequest();

        if ('api_app_user_action_requests_post_collection' !== $request->attributes->get('_route')) {
            return;
        }

        $changePasswordRequest = $event->getControllerResult();

        // $registration = $this->registrationRepository->findOneBy(['id' => $changePasswordRequest->ident]);
        // $count = $changePasswordRequest->count;

        // We do nothing if the user does not exist in the database
        // $this->registrationRepository->findByNothingSendInformationEmail($registration);
        $out = $this->appUserManager->changePassword(
            $changePasswordRequest->uid,
            $changePasswordRequest->password
        );
        // $url = "../" . $out;
        // $data = ["download" => $url];
        /*
        $response = new Response(
            $out,
            Response::HTTP_CREATED,
            array('content-type' => 'application/pdf; charset=utf-8')
        );
        $response->setCharset('UTF-8');
        */

        $data = ['data' => chunk_split(base64_encode($out))];

        $event->setResponse(new JsonResponse($data, 201));

        $event->setResponse(new JsonResponse(null, 204));


        // $event->setResponse($response);
    }
}
