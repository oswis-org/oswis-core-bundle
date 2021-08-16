<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\EventSubscriber\LexikJwt;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JwtPayloadAttachSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_CREATED => [['onJwtCreated']],
        ];
    }

    final public function onJwtCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        if ($user instanceof AppUser) {
            $payload = $event->getData();
            $payload['id'] = $user->getId();
            $payload['eMail'] = $user->getEMail();
            $payload['givenName'] = $user->getGivenName();
            $payload['fullName'] = $user->getFullName();
            $payload['type'] = $user->getAppUserType()?->getSlug();
            $event->setData($payload);
        }
    }
}
