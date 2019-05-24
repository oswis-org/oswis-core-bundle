<?php

namespace Zakjakub\OswisCoreBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Manager\AppUserManager;
use Zakjakub\OswisCoreBundle\Service\EmailSender;
use function assert;

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
     * @var EmailSender
     */
    private $emailSender;

    /**
     * ReservationSubscriber constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface       $em
     * @param LoggerInterface              $logger
     * @param EmailSender                  $emailSender
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        EmailSender $emailSender
    ) {
        // \error_log('Constructing ReservationSubscriber.');
        $this->encoder = $encoder;
        $this->em = $em;
        $this->logger = $logger;
        $this->emailSender = $emailSender;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['makeAppUser', EventPriorities::POST_WRITE],
        ];
    }

    /**
     * @param ViewEvent $event
     *
     * @throws ErrorException
     * @throws SuspiciousOperationException
     */
    public function makeAppUser(ViewEvent $event): void
    {
        $appUser = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        if (!$appUser instanceof AppUser || Request::METHOD_POST !== $method) {
            return;
        }
        assert($appUser instanceof AppUser);
        $appUserManager = new AppUserManager($this->encoder, $this->em, $this->logger, $this->emailSender);
        $appUserManager->appUserAction($appUser, 'activation-request');
    }

}
