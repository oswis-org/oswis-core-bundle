<?php

namespace Zakjakub\OswisCoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Exceptions\OswisException;
use Zakjakub\OswisCoreBundle\Manager\AppUserManager;
use Zakjakub\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use function assert;

/** @noinspection ClassNameCollisionInspection */

final class AppUserSubscriber implements EventSubscriberInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var OswisCoreSettingsProvider
     */
    private $oswisCoreSettings;

    /**
     * ReservationSubscriber constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface       $em
     * @param LoggerInterface              $logger
     * @param MailerInterface              $mailer
     * @param OswisCoreSettingsProvider    $oswisCoreSettings
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        MailerInterface $mailer,
        OswisCoreSettingsProvider $oswisCoreSettings
    ) {
        // \error_log('Constructing ReservationSubscriber.');
        $this->encoder = $encoder;
        $this->em = $em;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->oswisCoreSettings = $oswisCoreSettings;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::VIEW => ['makeAppUser', EventPriorities::POST_WRITE]];
    }

    /**
     * @param ViewEvent $event
     *
     * @throws OswisException
     */
    public function makeAppUser(ViewEvent $event): void
    {
        $appUser = $event->getControllerResult();
        try {
            $method = $event->getRequest()->getMethod();
        } catch (Exception $e) {
            return;
        }
        if (!($appUser instanceof AppUser) || Request::METHOD_POST !== $method) {
            return;
        }
        assert($appUser instanceof AppUser);
        $appUserManager = new AppUserManager($this->encoder, $this->em, $this->logger, $this->mailer, $this->oswisCoreSettings);
        $appUserManager->appUserAction($appUser, 'activation-request');
    }

}
