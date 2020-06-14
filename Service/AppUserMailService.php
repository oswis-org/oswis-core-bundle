<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEMail;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserToken;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Exception\LogicException as MimeLogicException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppUserMailService extends AbstractMailService
{
    public const TYPE_PASSWORD_CHANGE = AppUserService::PASSWORD_CHANGE;
    public const TYPE_PASSWORD_CHANGE_REQUEST = AppUserService::PASSWORD_CHANGE_REQUEST;
    public const TYPE_ACTIVATION = AppUserService::ACTIVATION;
    public const TYPE_ACTIVATION_REQUEST = AppUserService::ACTIVATION_REQUEST;

    public const ALLOWED_TYPES = [
        self::TYPE_PASSWORD_CHANGE,
        self::TYPE_PASSWORD_CHANGE_REQUEST,
        self::TYPE_ACTIVATION,
        self::TYPE_ACTIVATION_REQUEST,
    ];

    public const TYPES_SETTINGS = [
        self::TYPE_PASSWORD_CHANGE         => ['title' => 'Heslo změněno'],
        self::TYPE_PASSWORD_CHANGE_REQUEST => ['title' => 'Požadavek na změnu hesla'],
        self::TYPE_ACTIVATION              => ['title' => 'Účet byl aktivován'],
        self::TYPE_ACTIVATION_REQUEST      => ['title' => 'Aktivace uživatelského účtu'],
    ];

    protected UserPasswordEncoderInterface $encoder;

    public function __construct(
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        MailerInterface $mailer,
        OswisCoreSettingsProvider $oswisCoreSettings
    ) {
        parent::__construct($em, $logger, $mailer, $oswisCoreSettings);
        $this->encoder = $encoder;
    }

    /**
     * @param AppUser           $appUser
     * @param string            $type
     * @param AppUserToken|null $appUserToken
     *
     * @throws MimeLogicException|NotImplementedException|TransportExceptionInterface
     * @throws OswisException|InvalidTypeException
     */
    public function sendAppUserEMail(AppUser $appUser, string $type, ?AppUserToken $appUserToken = null): void
    {
        $isIS = false;
        if (null !== $appUserToken && $appUserToken->getAppUser() !== $appUser) {
            throw new OswisException('Token není kompatibilní s uživatelem.');
        }
        if (empty($typeSettings = self::TYPES_SETTINGS[$type] ?? null) || empty($title = ($typeSettings['title'] ?? null))) {
            throw new NotImplementedException($type, 'u uživatelských účtů');
        }
        $data = [
            'appUser'      => $appUser,
            'type'         => $type,
            'appUserToken' => $appUserToken,
            'isIS'         => $isIS,
        ];
        $appUserEMail = new AppUserEMail($appUser, new Nameable($title), $appUser->getEmail(), $type, $appUserToken);
        $this->em->persist($appUserEMail);
        try {
            $this->sendEMail($appUserEMail, '@OswisOrgOswisCore/e-mail/pages/app-user.html.twig', $data, $appUser->getName());
        } catch (TransportExceptionInterface|MimeLogicException $exception) {
            $this->logger->error('App user e-mail exception: '.$exception->getMessage());
            $appUserEMail->setInternalNote($exception->getMessage());
            $this->em->flush();
            throw $exception;
        }
        $this->em->flush();
    }
}
