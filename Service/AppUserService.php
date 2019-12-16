<?php

namespace Zakjakub\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Entity\AppUserType;
use Zakjakub\OswisCoreBundle\Exceptions\OswisException;
use Zakjakub\OswisCoreBundle\Exceptions\OswisNotImplementedException;
use Zakjakub\OswisCoreBundle\Exceptions\OswisUserNotUniqueException;
use Zakjakub\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use Zakjakub\OswisCoreBundle\Repository\AppUserRepository;
use Zakjakub\OswisCoreBundle\Utils\EmailUtils;
use Zakjakub\OswisCoreBundle\Utils\StringUtils;
use function random_int;

class AppUserService
{
    public const PASSWORD_CHANGE = 'password-change';
    public const PASSWORD_CHANGE_REQUEST = 'password-change-request';
    public const ACTIVATION = 'activation';
    public const ACTIVATION_REQUEST = 'activation-request';

    public const ALLOWED_TYPES = [self::PASSWORD_CHANGE, self::PASSWORD_CHANGE_REQUEST, self::ACTIVATION, self::ACTIVATION_REQUEST];

    protected EntityManagerInterface $em;

    protected LoggerInterface $logger;

    private UserPasswordEncoderInterface $encoder;

    private MailerInterface $mailer;

    private OswisCoreSettingsProvider $oswisCoreSettings;

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
     * Create and save new user of application.
     *
     * @param bool|null $activate
     *
     * @throws OswisException
     * @throws OswisUserNotUniqueException
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
        try {
            $username ??= 'user'.random_int(1, 9999);
        } catch (Exception $e) {
            $username ??= 'user';
        }
        $email ??= $username.'@jakubzak.eu'; // TODO: Change to @oswis.org and redirect mails.
        $token = null;
        $appUserRepo = $this->em->getRepository(AppUser::class);
        assert($appUserRepo instanceof AppUserRepository);
        $appUser = $appUserRepo->findOneBy(['email' => $email]) ?? $appUserRepo->findOneBy(['username' => $username]);
        assert($appUser instanceof AppUser);
        if ($appUser && !$errorWhenExist) {
            $this->logger->notice('Skipped existing user '.$appUser->getUsername().' '.$appUser->getEmail().'.');

            return $appUser;
        }
        if ($appUser && $errorWhenExist) {
            throw new OswisUserNotUniqueException('User '.$appUser->getUsername().' already exist.');
        }
        $appUser = new AppUser($fullName, $username, $email, null, null);
        $appUser->setAppUserType($appUserType);
        if ($activate) {
            $password ??= StringUtils::generatePassword();
            $appUser->setPassword($this->encoder->encodePassword($appUser, $password));
            $this->appUserAction($appUser, self::ACTIVATION, $password, null, $sendMail, true);
        } else {
            $this->appUserAction($appUser, self::ACTIVATION_REQUEST, null, null, $sendMail);
        }
        $this->em->persist($appUser);
        $this->em->flush();
        $this->logger->info('Created user '.$appUser->getUsername().', type: '.($appUserType ? $appUserType->getName() : null));

        return $appUser;
    }

    /**
     * @throws OswisException
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
            if (self::PASSWORD_CHANGE_REQUEST === $type) { // Create token for password reset/change and send it to user by e-mail.
                $token = $appUser->generatePasswordRequestToken();
                if ($sendConfirmation) {
                    try {
                        $this->sendPasswordEmail($appUser, self::PASSWORD_CHANGE_REQUEST, $token);
                    } catch (TransportExceptionInterface $e) {
                        $this->logger->error($e->getMessage());
                        throw new OswisException('Nepodařilo se odeslat informační e-mail o změně hesla.');
                    }
                }
            } elseif (self::PASSWORD_CHANGE === $type) { // Check token for password reset/change and change password for user.
                if (!$withoutToken && !$token) {
                    throw new InvalidArgumentException('Token pro změnu hesla nebyl zadán.');
                }
                if (!$withoutToken && !$appUser->checkAndDestroyPasswordResetRequestToken($token)) {
                    throw new InvalidArgumentException('Token pro změnu hesla není platný (neexistuje nebo vypršela jeho platnost).');
                }
                $random = empty($password);
                $password ??= StringUtils::generatePassword();
                $appUser->setPassword($this->encoder->encodePassword($appUser, $password));
                if ($sendConfirmation) {
                    try {
                        $this->sendPasswordEmail($appUser, self::PASSWORD_CHANGE, null, $random ? $password : null);
                    } catch (TransportExceptionInterface $e) {
                        $this->logger->error($e->getMessage());
                        throw new OswisException('Nepodařilo se odeslat e-mail s požadavkem na změnu hesla.');
                    }
                }
            } elseif (self::ACTIVATION_REQUEST === $type) { // Generate token for account activation and send it to user by e-mail.
                try {
                    $this->sendAppUserEmail($appUser, self::ACTIVATION_REQUEST, $appUser->generateAccountActivationRequestToken());
                } catch (TransportExceptionInterface $e) {
                    $this->logger->error($e->getMessage());
                    throw new OswisException('Nepodařilo se odeslat e-mail s požadavkem na aktivaci účtu.');
                }
            } elseif (self::ACTIVATION === $type) { // Check activation token and activate account.
                if (!$withoutToken && !$token) {
                    throw new InvalidArgumentException('Token pro aktivaci účtu nebyl zadán.');
                }
                if (!$withoutToken && !$appUser->checkAndDestroyAccountActivationRequestToken($token)) {
                    throw new InvalidArgumentException('Token pro aktivaci účtu není platný (neexistuje nebo vypršela jeho platnost).');
                }
                $random = empty($password);
                $password ??= StringUtils::generatePassword();
                $appUser->setPassword($this->encoder->encodePassword($appUser, $password));
                if ($sendConfirmation) {
                    try {
                        $this->sendAppUserEmail($appUser, self::ACTIVATION, $token, $random ? $password : null);
                    } catch (TransportExceptionInterface $e) {
                        $this->logger->error($e->getMessage());
                        throw new OswisException('Nepodařilo se odeslat informační e-mail o aktivaci účtu.');
                    }
                }
            } else { // Type is not recognized.
                throw new OswisNotImplementedException($type, 'u uživatelských účtů');
            }
            $this->em->persist($appUser);
            $this->em->flush();

            return true;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new OswisException('Nastal problém při změně uživatelského účtu. '.$e->getMessage());
        }
    }

    /**
     * @throws ErrorException
     * @throws TransportExceptionInterface
     */
    final public function sendPasswordEmail(AppUser $appUser, string $type, ?string $token = null, string $password = null): void
    {
        try {
            if (!$appUser) {
                throw new InvalidArgumentException('Uživatel nenalezen.');
            }
            $title = null;
            if (self::PASSWORD_CHANGE === $type) { // Send e-mail about password change. Include password if present (it means that it's generated randomly).
                $title = 'Heslo změněno';
                $token = null;
            } elseif (self::PASSWORD_CHANGE_REQUEST === $type) { // Send e-mail about password reset request. Include token for change.
                $title = 'Požadavek na změnu hesla';
                $password = null;
            } else {
                throw new OswisNotImplementedException($type, 'u změny hesla');
            }
            $data = [
                'type'     => $type,
                'appUser'  => $appUser,
                'token'    => $token,
                'password' => $password,
                'logo'     => 'cid:logo',
                'oswis'    => $this->oswisCoreSettings,
            ];
            $name = $appUser->getFullName() ?? $appUser->getUsername() ?? '';
            $email = new TemplatedEmail();
            $email->to(new Address($appUser->getEmail() ?? '', self::mimeEnc($name)));
            $email->subject(self::mimeEnc($title));
            $email->htmlTemplate('@ZakjakubOswisCore/e-mail/password.html.twig')->context($data);
            $this->mailer->send($email);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new ErrorException('Problém s odesláním zprávy o změně hesla.  '.$e->getMessage());
        }
    }

    private static function mimeEnc(?string $input = null): string
    {
        return EmailUtils::mime_header_encode($input);
    }

    /**
     * @throws ErrorException
     * @throws TransportExceptionInterface
     */
    final public function sendAppUserEmail(AppUser $appUser, string $type, ?string $token = null, ?string $password = null): void
    {
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
            $data = [
                'appUser'  => $appUser,
                'type'     => $type,
                'token'    => $token,
                'password' => $password,
                'logo'     => 'cid_logo',
                'oswis'    => $this->oswisCoreSettings,
            ];
            $name = $appUser->getFullName() ?? $appUser->getUsername() ?? '';
            $receiverAddress = new Address($appUser->getEmail() ?? '', self::mimeEnc($name));
            $email = new TemplatedEmail();
            $email->to($receiverAddress)->subject(self::mimeEnc($title));
            $email->htmlTemplate('@ZakjakubOswisCore/e-mail/app-user.html.twig')->context($data);
            $this->mailer->send($email);
        } catch (Exception $e) {
            throw new ErrorException('Problém s odesláním zprávy o změně účtu:  '.$e->getMessage());
        }
    }
}