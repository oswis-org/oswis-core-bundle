<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use LogicException;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserToken;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Exceptions\TokenInvalidException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotUniqueException;
use OswisOrg\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use OswisOrg\OswisCoreBundle\Repository\AppUserRepository;
use OswisOrg\OswisCoreBundle\Utils\StringUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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

    protected UserPasswordEncoderInterface $encoder;

    protected MailerInterface $mailer;

    protected OswisCoreSettingsProvider $oswisCoreSettings;

    protected AppUserTypeService $appUserTypeService;

    protected AppUserRoleService $appUserRoleService;

    protected AppUserTokenService $appUserTokenService;

    public function __construct(
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        MailerInterface $mailer,
        OswisCoreSettingsProvider $oswisCoreSettings,
        AppUserTypeService $appUserTypeService,
        AppUserRoleService $appUserRoleService,
        AppUserTokenService $appUserTokenService
    ) {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->oswisCoreSettings = $oswisCoreSettings;
        $this->appUserTypeService = $appUserTypeService;
        $this->appUserRoleService = $appUserRoleService;
        $this->appUserTokenService = $appUserTokenService;
    }

    public function getNewRandomUsername(): string
    {
        try {
            $number = random_int(1, 9999);
        } catch (Exception $e) {
            $number = time();
        }

        return "user$number";
    }

    /**
     * Create and save new user of application.
     *
     * @param AppUser|null $appUser
     * @param string|null  $password
     * @param bool|null    $activate
     * @param bool|null    $sendMail
     * @param bool|null    $skipDuplicityError
     *
     * @return AppUser
     * @throws InvalidTypeException
     * @throws OswisException
     * @throws UserNotFoundException
     * @throws UserNotUniqueException
     * @todo Refactor: AppUser instance instead of single arguments.
     */
    public function create(?AppUser $appUser = null, ?string $password = null, ?bool $activate = false, ?bool $sendMail = false, ?bool $skipDuplicityError = true): AppUser
    {
        if (null === $appUser) {
            throw new UserNotFoundException();
        }
        if (empty($appUser->getUsername())) {
            $appUser->setUsername($this->getNewRandomUsername());
        }
        $username = $appUser->getUsername();
        if (empty($appUser->getEmail())) {
            $appUser->setEmail("$username@oswis.org");
        }
        $email = $appUser->getEmail();
        $existingAppUser = $this->getRepository()->findOneBy(['email' => $email]) ?? $this->getRepository()->findOneBy(['username' => $username]);
        if (null !== $existingAppUser) {
            $existingId = $existingAppUser->getId();
            if (!$skipDuplicityError) {
                throw new UserNotUniqueException("Uživatel $username/$email již existuje.");
            }
            $this->logger->notice("Skipped existing user $existingId $username $email.");

            return $appUser;
        }
        true === $activate ? $this->activate($appUser, $password, $sendMail) : $this->requestActivation($appUser);
        $this->em->persist($appUser);
        $this->em->flush();
        $id = $appUser->getId();
        $this->logger->info("Created user $id/$username/$email.");

        return $appUser;
    }

    public function getRepository(): AppUserRepository
    {
        $repository = $this->em->getRepository(AppUser::class);
        assert($repository instanceof AppUserRepository);

        return $repository;
    }

    /**
     * @param AppUser|null $appUser
     * @param string       $type
     * @param bool|null    $sendConfirmation
     *
     * @throws InvalidTypeException
     * @throws NotImplementedException
     * @throws OswisException
     * @throws UserNotFoundException
     */
    public function appUserAction(?AppUser $appUser, string $type, ?bool $sendConfirmation = true): void
    {
        try {
            if (self::PASSWORD_CHANGE_REQUEST === $type) { // Create token for password reset/change and send it to user by e-mail.
                $this->requestPasswordChange($appUser, $sendConfirmation);
            } elseif (self::ACTIVATION_REQUEST === $type) { // Generate token for account activation and send it to user by e-mail.
                $this->requestActivation($appUser);
            } else { // Action type is not recognized.
                throw new NotImplementedException($type, 'u uživatelských účtů');
            }
            if ($appUser) {
                $this->em->persist($appUser);
            }
            $this->em->flush();
        } catch (OswisException $e) {
            $this->logger->error('[ERROR] '.$e->getMessage());
            throw $e;
        }
    }

    public function activate(AppUser $appUser, ?string $password, bool $sendConfirmation = true): void
    {
        try {
            $isRandom = empty($password);
            $password ??= StringUtils::generatePassword();
            $appUser->setPassword($this->encoder->encodePassword($appUser, $password));
            if ($sendConfirmation) {
                $this->sendAppUserEmail($appUser, self::ACTIVATION, null, $isRandom ? $password : null);
            }
            $this->em->persist($appUser);
            $this->logger->info('Successfully activated user ('.$appUser->getId().').');
        } catch (OswisException $exception) {
            $this->logger->error('User ('.$appUser->getId().') activation FAILED. '.$exception->getMessage());
        }
    }

    /**
     * @throws OswisException
     */
    public function sendPasswordEmail(AppUser $appUser, string $type, ?AppUserToken $appUserToken = null, string $newPassword = null): void
    {
        try {
            if (self::PASSWORD_CHANGE === $type) { // Send e-mail about password change. Include password if present (it means that it's generated randomly).
                $title = 'Heslo změněno';
                $appUserToken = null;
            } elseif (self::PASSWORD_CHANGE_REQUEST === $type) { // Send e-mail about password reset request. Include token for change.
                $title = 'Požadavek na změnu hesla';
                $newPassword = null;
            } else {
                throw new NotImplementedException($type, 'u změny hesla');
            }
            $data = [
                'type'         => $type,
                'appUser'      => $appUser,
                'appUserToken' => $appUserToken,
                'password'     => $newPassword,
            ];
            $email = new TemplatedEmail();
            try {
                $email->to(new Address($appUser->getEmail() ?? '', $appUser->getName()));
            } catch (LogicException $e) {
                $email->to($appUser->getEmail() ?? '');
            }
            $email->subject($title)->htmlTemplate('@OswisOrgOswisCore/e-mail/password.html.twig')->context($data);
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error($e->getMessage());
            throw new OswisException('Problém s odesláním zprávy o změně hesla.  '.$e->getMessage());
        }
    }

    /**
     * @param AppUser|null $appUser
     * @param string|null  $newPassword
     * @param string|null  $token
     * @param bool         $sendConfirmation
     * @param bool         $withoutToken
     *
     * @throws OswisException
     */
    public function changePassword(AppUser $appUser, ?string $newPassword, bool $sendConfirmation): void
    {
        $random = empty($newPassword);
        $newPassword ??= StringUtils::generatePassword();
        $appUser->setPassword($this->encoder->encodePassword($appUser, $newPassword));
        if ($sendConfirmation) {
            $this->sendPasswordEmail($appUser, self::PASSWORD_CHANGE, null, $random ? $newPassword : null);
        }
        $this->logger->info('Password changed for user '.$appUser->getId().'.');
    }

    /**
     * @param AppUser|null $appUser
     *
     * @throws InvalidTypeException
     * @throws OswisException
     * @throws UserNotFoundException
     */
    private function requestActivation(?AppUser $appUser): void
    {
        if (null === $appUser) {
            throw new UserNotFoundException();
        }
        $appUserToken = $this->appUserTokenService->create($appUser, AppUserToken::TYPE_ACTIVATION, false);
        $this->sendAppUserEmail($appUser, self::ACTIVATION_REQUEST, $appUserToken);
        $this->logger->info('Created activation request for user '.$appUser->getId().'.');
    }

    /**
     * @param string $token
     * @param int    $appUserId
     *
     * @throws TokenInvalidException
     */
    public function getVerifiedToken(string $token, int $appUserId): AppUserToken
    {
        $appUserToken = $this->getToken($token, $appUserId);
        if (null === $appUserToken || null === $appUserToken->getAppUser()) {
            throw new TokenInvalidException('zadaný token neexistuje');
        }
        $appUserToken->use(true);

        return $appUserToken;
    }

    public function getToken(string $token, int $appUserId): ?AppUserToken
    {
        return $this->appUserTokenService->getRepository()->findByToken($token, $appUserId);
    }

    /**
     * @param string      $token
     * @param int         $appUserId
     * @param string|null $newPassword
     *
     * @return string Type of processed action as string (constant from AppUserService).
     * @throws OswisException
     * @throws TokenInvalidException
     */
    public function processToken(string $token, int $appUserId, ?string $newPassword = null): void
    {
        $appUserToken = $this->getToken($token, $appUserId);
        if (null === $appUserToken || null === ($appUser = $appUserToken->getAppUser())) {
            throw new TokenInvalidException('zadaný token neexistuje');
        }
        try {
            $type = $appUserToken->getType();
            $appUserToken->use();
            if (AppUserToken::TYPE_ACTIVATION === $type) {
                $this->activate($appUser, null, true);
            }
            if (AppUserToken::TYPE_PASSWORD_RESET === $type) {
                $this->changePassword($appUser, $newPassword, true);
            }
            throw new TokenInvalidException('neznámý typ tokenu', $token);
        } catch (OswisException|TokenInvalidException $exception) {
            $this->logger->error('Problem occurred when processing app user token. '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @param AppUser|null $appUser
     * @param bool         $sendConfirmation
     *
     * @throws OswisException
     * @throws UserNotFoundException
     * @throws InvalidTypeException
     */
    public function requestPasswordChange(?AppUser $appUser, bool $sendConfirmation): void
    {
        if (null === $appUser) {
            throw new UserNotFoundException();
        }
        $appUserToken = $this->appUserTokenService->create($appUser, AppUserToken::TYPE_PASSWORD_RESET, false);
        if ($sendConfirmation) {
            $this->sendPasswordEmail($appUser, self::PASSWORD_CHANGE_REQUEST, $appUserToken);
        }
        $this->logger->info('Created password change request for user '.$appUser->getId().'.');
    }

    /**
     * @throws OswisException
     */
    public function sendAppUserEmail(AppUser $appUser, string $type, ?AppUserToken $appUserToken = null, ?string $newPassword = null): void
    {
        try {
            if (self::ACTIVATION_REQUEST === $type) { // Send e-mail about activation request. Include token for activation.
                $title = 'Aktivace uživatelského účtu';
            } elseif (self::ACTIVATION === $type) {
                // Send e-mail about account activation. Include password if present (it means that it's generated randomly).
                $title = 'Účet byl aktivován';
            } else {
                throw new OswisException('Akce "'.$type.'" není u uživatelských účtů implementována.');
            }
            $data = [
                'appUser'      => $appUser,
                'type'         => $type,
                'appUserToken' => $appUserToken,
                'password'     => $newPassword,
            ];
            $email = new TemplatedEmail();
            try {
                $email->to(new Address($appUser->getEmail() ?? '', $appUser->getName()));
            } catch (LogicException $e) {
                $email->to($appUser->getEmail() ?? '');
            }
            $email->subject($title)->htmlTemplate('@OswisOrgOswisCore/e-mail/app-user.html.twig')->context($data);
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new OswisException('Problém s odesláním zprávy o změně účtu.  '.$e->getMessage());
        }
    }
}
