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

final class AppUserSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @var OswisCoreSettingsProvider
     */
    private OswisCoreSettingsProvider $oswisCoreSettings;

    /**
     * ReservationSubscriber constructor.
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

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::VIEW => ['makeAppUser', EventPriorities::POST_WRITE]];
    }

    /** @noinspection PhpUnused */
    /**
     * @throws OswisException
     */
    public function makeAppUser(ViewEvent $event): void
    {
        $appUser = $event->getControllerResult();
        if (!($appUser instanceof AppUser)) {
            return;
        }
        try {
            $method = $event->getRequest()->getMethod();
        } catch (Exception $e) {
            return;
        }
        if (Request::METHOD_POST !== $method) {
            return;
        }
        $appUserManager = new AppUserManager($this->encoder, $this->em, $this->logger, $this->mailer, $this->oswisCoreSettings);
        $appUserManager->appUserAction($appUser, 'activation-request');
    }
}
