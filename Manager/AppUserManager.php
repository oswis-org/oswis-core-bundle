<?php

namespace Zakjakub\OswisCoreBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\NamedAddress;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Entity\AppUserType;
use Zakjakub\OswisCoreBundle\Exceptions\OswisUserNotUniqueException;
use Zakjakub\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use Zakjakub\OswisCoreBundle\Utils\EmailUtils;
use Zakjakub\OswisCoreBundle\Utils\StringUtils;
use function random_int;

/**
 * Class AppUserManager
 * @package Zakjakub\OswisCoreBundle\Manager
 */
class AppUserManager
{
    public const RESET = 'reset';
    public const RESET_REQUEST = 'reset-request';
    public const ACTIVATION = 'activation';
    public const ACTIVATION_REQUEST = 'activation-request';

    public const ALLOWED_TYPES = [self::RESET, self::RESET_REQUEST, self::ACTIVATION, self::ACTIVATION_REQUEST];

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var LoggerInterface
     */
    protected $logger;

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
     * AppUserManager constructor.
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
        $this->em = $em;
        $this->encoder = $encoder;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->oswisCoreSettings = $oswisCoreSettings;
    }

    /**
     * @param string|null      $fullName
     * @param AppUserType|null $appUserType
     * @param string|null      $username
     * @param string|null      $password
     * @param string|null      $email
     * @param bool|null        $activate
     * @param bool|null        $sendMail
     * @param bool|null        $errorWhenExist
     *
     * @return AppUser
     * @throws ErrorException
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    final public function create(
        ?string $fullName = null,
        ?AppUserType $appUserType = null,
        ?string $username = null,
        ?string $password = null,
        ?string $email = null,
        ?bool $activate = false,
        ?bool $sendMail = false,
        ?bool $errorWhenExist = true
    ): AppUser {
        $username = $username ?? 'user'.random_int(1, 9999);
        $email = $email ?? $username.'@jakubzak.eu';
        $em = $this->em;
        $token = null;
        $appUserRoleRepo = $em->getRepository(AppUser::class);
        $appUser = $appUserRoleRepo->findOneBy(['email' => $email]);

        if (!$appUser) {
            $appUser = $appUserRoleRepo->findOneBy(['username' => $username]);
        }

        if ($appUser && !$errorWhenExist) {
            $this->logger->notice('Skipped existing user: '.$appUser->getUsername().' '.$appUser->getEmail().'.');

            return $appUser;
        }

        if ($appUser && $errorWhenExist) {
            throw new OswisUserNotUniqueException('User: '.$appUser->getUsername().' already exist.');
        }

        $appUser = new AppUser($fullName, $username, $email, null, null);
        $appUser->setAppUserType($appUserType);
        if ($activate) {
            $password = $password ?? StringUtils::generatePassword();
            $appUser->setPassword($this->encoder->encodePassword($appUser, $password));
            $this->appUserAction($appUser, self::ACTIVATION, $password, null, $sendMail, true);
        } else {
            $this->appUserAction($appUser, self::ACTIVATION_REQUEST, null, null, $sendMail);
        }
        $em->persist($appUser);
        $em->flush();
        $this->logger->info('Created user: '.$appUser->getUsername().', type: '.($appUserType ? $appUserType->getName() : null));

        return $appUser;
    }

    /**
     * @param AppUser     $appUser
     * @param string      $type
     * @param string|null $password
     * @param string|null $token
     * @param bool|null   $sendConfirmation
     * @param bool|null   $withoutToken
     *
     * @return bool
     * @throws ErrorException
     * @throws TransportExceptionInterface
     */
    final public function appUserAction(
        AppUser $appUser,
        string $type,
        ?string $password = null,
        ?string $token = null,
        ?bool $sendConfirmation = true,
        ?bool $withoutToken = false
    ): bool {
        try {
            if ($type === self::RESET_REQUEST) {
                // Create token for password reset/change and send it to user by e-mail.
                $token = $appUser->generatePasswordRequestToken();
                if ($sendConfirmation) {
                    $this->sendPasswordEmail($appUser, self::RESET_REQUEST, $token);
                }
            } elseif ($type === self::RESET) {
                // Check token for password reset/change and change password for user.
                if (!$withoutToken && !$token) {
                    throw new InvalidArgumentException('Token nenalezen.');
                }
                if (!$withoutToken && !$appUser->checkAndDestroyPasswordResetRequestToken($token)) {
                    throw new InvalidArgumentException('Špatný token.');
                }
                $random = $password ? false : true;
                $password = $password ?? StringUtils::generatePassword();
                $appUser->setPassword($this->encoder->encodePassword($appUser, $password));
                if ($sendConfirmation) {
                    $this->sendPasswordEmail($appUser, self::RESET, null, $random ? $password : null);
                }
            } elseif ($type === self::ACTIVATION_REQUEST) {
                // Generate token for account activation and send it to user by e-mail.
                $this->sendAppUserEmail($appUser, self::ACTIVATION_REQUEST, $appUser->generateAccountActivationRequestToken());
            } elseif ($type === self::ACTIVATION) {
                // Check activation token and activate account.
                if (!$withoutToken && !$token) {
                    throw new InvalidArgumentException('Token nenalezen.');
                }
                if (!$withoutToken && !$appUser->checkAndDestroyAccountActivationRequestToken($token)) {
                    throw new InvalidArgumentException('Špatný token.');
                }
                $random = $password ? false : true;
                $password = $password ?? StringUtils::generatePassword();
                $appUser->setPassword($this->encoder->encodePassword($appUser, $password));
                if ($sendConfirmation) {
                    $this->sendAppUserEmail($appUser, self::ACTIVATION, $token, $random ? $password : null);
                }
            } else { // Type is not recognized.
                throw new InvalidArgumentException('Akce "'.$type.'" není u uživatelských účtů implementována.');
            }
            $this->em->persist($appUser);
            $this->em->flush();

            return true;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new ErrorException('Nastal problém při změně uživatelského účtu ('.$e->getMessage().').');
        }
    }

    /**
     * @param AppUser     $appUser
     * @param string      $type
     * @param string|null $token
     * @param string|null $password
     *
     * @throws ErrorException
     * @throws TransportExceptionInterface
     */
    final public function sendPasswordEmail(
        AppUser $appUser,
        string $type,
        ?string $token = null,
        string $password = null
    ): void {
        try {
            if (!$appUser) {
                throw new InvalidArgumentException('Uživatel nenalezen.');
            }

            $title = null;

            if (self::RESET === $type) { // Send e-mail about password change. Include password if present (it means that it's generated randomly).
                $title = 'Heslo změněno';
                $token = null;
            } elseif (self::RESET_REQUEST === $type) { // Send e-mail about password reset request. Include token for change.
                $title = 'Požadavek na změnu hesla';
                $password = null;
            } else {
                throw new InvalidArgumentException('Akce "'.$type.'" není u změny hesla implementována.');
            }

            $data = array(
                'type'     => $type,
                'appUser'  => $appUser,
                'token'    => $token,
                'password' => $password,
            );

            $email = (new TemplatedEmail())
                ->to(
                    new NamedAddress(
                        $appUser->getEmail() ?? '',
                        EmailUtils::mime_header_encode($appUser->getFullName() ?? $appUser->getUsername() ?? '')
                    )
                )
                ->subject(EmailUtils::mime_header_encode($title))
                ->htmlTemplate('@ZakjakubOswisCore/e-mail/password.html.twig')
                ->context($data);
            $this->mailer->send($email);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new ErrorException('Problém s odesláním zprávy o změně hesla.  '.$e->getMessage());
        }
    }

    /**
     * @param AppUser     $appUser
     * @param string      $type
     * @param string|null $token
     * @param string|null $password
     *
     * @throws ErrorException
     * @throws TransportExceptionInterface
     */
    final public function sendAppUserEmail(
        AppUser $appUser,
        string $type,
        ?string $token = null,
        ?string $password = null
    ): void {
        try {
            if (!$appUser) {
                throw new ErrorException('Uživatel nenalezen.');
            }

            if (self::ACTIVATION_REQUEST === $type) { // Send e-mail about activation request. Include token for activation.
                $title = 'Aktivace uživatelského účtu';
            } elseif (self::ACTIVATION === $type) {
                // Send e-mail about account activation. Include password if present (it means that it's generated randomly).
                $title = 'Účet byl aktivován';
            } else {
                throw new InvalidArgumentException('Akce "'.$type.'" není u uživatelských účtů implementována.');
            }

            $data = array(
                'appUser'  => $appUser,
                'type'     => $type,
                'token'    => $token,
                'password' => $password,
                'logo'     => 'cid_logo',
                'oswis'    => $this->oswisCoreSettings,
            );

            $receiverAddress = new NamedAddress(
                $appUser->getEmail() ?? '',
                EmailUtils::mime_header_encode($appUser->getFullName() ?? $appUser->getUsername() ?? '')
            );

            $email = (new TemplatedEmail())
                ->to($receiverAddress)
                ->subject(EmailUtils::mime_header_encode($title))
                ->htmlTemplate('@ZakjakubOswisCore/e-mail/app-user.html.twig')
                ->context($data);

            $this->mailer->send($email);
        } catch (Exception $e) {
            throw new ErrorException('Problém s odesláním zprávy o změně účtu:  '.$e->getMessage());
        }
    }
}
