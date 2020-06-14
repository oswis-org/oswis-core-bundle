<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Exception\LogicException as MimeLogicException;
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

    protected OswisCoreSettingsProvider $oswisCoreSettings;

    protected AppUserTokenService $appUserTokenService;

    protected AppUserMailService $appUserMailService;

    public function __construct(
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        OswisCoreSettingsProvider $oswisCoreSettings,
        AppUserTokenService $appUserTokenService,
        AppUserMailService $appUserMailService
    ) {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->logger = $logger;
        $this->oswisCoreSettings = $oswisCoreSettings;
        $this->appUserMailService = $appUserMailService;
        $this->appUserTokenService = $appUserTokenService;
    }

    /**
     * Create and save new user of application.
     *
     * @param AppUser|null $appUser
     * @param bool|null    $activate
     * @param bool|null    $sendMail
     * @param bool|null    $skipDuplicityError
     *
     * @return AppUser
     * @throws OswisException|InvalidTypeException|NotImplementedException
     * @throws UserNotFoundException|UserNotUniqueException
     * @throws TransportExceptionInterface|MimeLogicException
     */
    public function create(?AppUser $appUser = null, ?bool $activate = false, ?bool $sendMail = false, ?bool $skipDuplicityError = true): AppUser
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
        true === $activate ? $this->activate($appUser, $sendMail) : $this->requestActivation($appUser);
        $this->em->persist($appUser);
        $this->em->flush();
        $id = $appUser->getId();
        $this->logger->info("Created user $id/$username/$email.");

        return $appUser;
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

    public function getRepository(): AppUserRepository
    {
        $repository = $this->em->getRepository(AppUser::class);
        assert($repository instanceof AppUserRepository);

        return $repository;
    }

    /**
     * @param AppUser $appUser
     * @param bool    $sendConfirmation
     *
     * @throws InvalidTypeException
     * @throws MimeLogicException|TransportExceptionInterface
     */
    public function activate(AppUser $appUser, bool $sendConfirmation = true): void
    {
        try {
            $isRandom = empty($appUser->getPlainPassword());
            $appUser->setPlainPassword($isRandom ? StringUtils::generatePassword() : $appUser->getPlainPassword(), $this->encoder, !$isRandom);
            if ($sendConfirmation) {
                $this->appUserMailService->sendAppUserEMail($appUser, self::ACTIVATION);
            }
            $this->em->persist($appUser);
            $this->em->flush();
            $this->logger->info('Successfully activated user ('.$appUser->getId().').');
        } catch (OswisException $exception) {
            $this->logger->error('User ('.$appUser->getId().') activation FAILED. '.$exception->getMessage());
        }
    }

    /**
     * @param AppUser|null $appUser
     *
     * @throws InvalidTypeException|MimeLogicException|TransportExceptionInterface
     * @throws OswisException|UserNotFoundException|NotImplementedException
     */
    public function requestActivation(?AppUser $appUser): void
    {
        try {
            if (null === $appUser) {
                throw new UserNotFoundException();
            }
            $appUserToken = $this->appUserTokenService->create($appUser, AppUserToken::TYPE_ACTIVATION, false);
            $this->appUserMailService->sendAppUserEMail($appUser, self::ACTIVATION_REQUEST, $appUserToken);
            $this->em->persist($appUser);
            $this->logger->info('Created and sent activation request for user '.$appUser->getId().'.');
        } catch (OswisException|InvalidTypeException|TransportExceptionInterface|MimeLogicException $exception) {
            $this->logger->error('User ('.$appUser->getId().') activation request FAILED. '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @param AppUser|null $appUser
     * @param bool         $sendConfirmation
     *
     * @throws MimeLogicException|TransportExceptionInterface
     * @throws OswisException|UserNotFoundException|InvalidTypeException|NotImplementedException
     */
    public function requestPasswordChange(?AppUser $appUser, bool $sendConfirmation): void
    {
        try {
            if (null === $appUser) {
                throw new UserNotFoundException();
            }
            $appUserToken = $this->appUserTokenService->create($appUser, AppUserToken::TYPE_PASSWORD_RESET, false);
            if ($sendConfirmation) {
                $this->appUserMailService->sendAppUserEMail($appUser, self::PASSWORD_CHANGE_REQUEST, $appUserToken);
            }
            $this->em->persist($appUser);
            $andSent = $sendConfirmation ? ' and sent' : '';
            $this->logger->info("Created $andSent password change request for user ".$appUser->getId().'.');
        } catch (OswisException|InvalidTypeException|TransportExceptionInterface|MimeLogicException $exception) {
            $this->logger->error('User ('.$appUser->getId().') password change request FAILED. '.$exception->getMessage());
            throw $exception;
        }
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
        if (null === $appUserToken) {
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
     * @throws MimeLogicException|TransportExceptionInterface
     * @throws NotImplementedException|OswisException|InvalidTypeException|TokenInvalidException
     */
    public function processToken(string $token, int $appUserId, ?string $newPassword = null): void
    {
        // TODO: Is it used somewhere?
        $appUserToken = $this->getToken($token, $appUserId);
        if (null === $appUserToken) {
            throw new TokenInvalidException('zadaný token neexistuje');
        }
        try {
            $type = $appUserToken->getType();
            $appUserToken->use();
            if (AppUserToken::TYPE_ACTIVATION === $type) {
                $this->activate($appUserToken->getAppUser(), true);
            }
            if (AppUserToken::TYPE_PASSWORD_RESET === $type) {
                $this->changePassword($appUserToken->getAppUser(), $newPassword, true);
            }
            throw new TokenInvalidException('neznámý typ tokenu', $token);
        } catch (OswisException|TokenInvalidException|InvalidTypeException|TransportExceptionInterface|MimeLogicException $exception) {
            $this->logger->error('Problem occurred when processing app user token. '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @param AppUser     $appUser
     * @param string|null $password
     * @param bool        $sendConfirmation
     *
     * @throws MimeLogicException|TransportExceptionInterface
     * @throws OswisException|InvalidTypeException|NotImplementedException
     */
    public function changePassword(AppUser $appUser, ?string $password, bool $sendConfirmation): void
    {
        try {
            $isRandom = empty($password);
            $password ??= StringUtils::generatePassword();
            $appUser->setPlainPassword($password, $this->encoder, !$isRandom);
            if ($sendConfirmation) {
                $this->appUserMailService->sendAppUserEMail($appUser, self::PASSWORD_CHANGE);
            }
            $this->em->persist($appUser);
            $this->em->flush();
            $this->logger->info('Successfully changed password for user ('.$appUser->getId().').');
        } catch (OswisException|InvalidTypeException|TransportExceptionInterface|MimeLogicException $exception) {
            $this->logger->error('Password change for user ('.$appUser->getId().') FAILED. '.$exception->getMessage());
            throw $exception;
        }
    }
}
