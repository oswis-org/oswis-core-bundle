<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserToken;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserType;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Exceptions\NotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Exceptions\TokenInvalidException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotUniqueException;
use OswisOrg\OswisCoreBundle\Repository\AppUserRepository;
use OswisOrg\OswisCoreBundle\Utils\StringUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

use function random_int;

class AppUserService
{
    public const PASSWORD_CHANGE = 'password-change';
    public const PASSWORD_CHANGE_REQUEST = 'password-change-request';
    public const ACTIVATION = 'activation';
    public const ACTIVATION_REQUEST = 'activation-request';

    public const ALLOWED_TYPES = [self::PASSWORD_CHANGE, self::PASSWORD_CHANGE_REQUEST, self::ACTIVATION, self::ACTIVATION_REQUEST];

    private EntityManagerInterface $em;

    private LoggerInterface $logger;

    private PasswordHasherInterface $encoder;

    private AppUserTokenService $appUserTokenService;

    private AppUserMailService $appUserMailService;

    private AppUserTypeService $appUserTypeService;

    private AppUserRepository $appUserRepository;

    public function __construct(
        PasswordHasherInterface $encoder,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        AppUserTokenService $appUserTokenService,
        AppUserMailService $appUserMailService,
        AppUserTypeService $appUserTypeService,
        AppUserRepository $appUserRepository
    ) {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->logger = $logger;
        $this->appUserMailService = $appUserMailService;
        $this->appUserTokenService = $appUserTokenService;
        $this->appUserTypeService = $appUserTypeService;
        $this->appUserRepository = $appUserRepository;
    }

    /**
     * Create and save new user of application.
     *
     * @param  AppUser|null  $appUser
     * @param  bool|null  $activate
     * @param  bool|null  $sendMail
     * @param  bool|null  $skipDuplicityError
     *
     * @return AppUser
     * @throws InvalidTypeException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     * @throws UserNotFoundException
     * @throws UserNotUniqueException
     */
    public function create(?AppUser $appUser = null, ?bool $activate = false, ?bool $sendMail = false, ?bool $skipDuplicityError = true): AppUser
    {
        if (null === $appUser) {
            throw new UserNotFoundException();
        }
        if (empty($username = $appUser->getUsername())) {
            $appUser->setUsername($username = $this->getNewRandomUsername());
        }
        if (empty($email = $appUser->getEmail())) {
            $appUser->setEmail($email = "$username@oswis.org");
        }
        $existingAppUser = $this->getRepository()->findOneBy(['email' => $email]) ?? $this->getRepository()->findOneBy(['username' => $username]);
        if (null !== $existingAppUser) {
            $existingId = $existingAppUser->getId();
            if (!$skipDuplicityError) {
                throw new UserNotUniqueException("Uživatel $username/$email již existuje.");
            }
            $this->logger->notice("Skipped existing user $existingId $username $email.");

            return $appUser;
        }
        if (null === $appUser->getAppUserType()) {
            $appUser->setAppUserType($this->getDefaultAppUserType());
        }
        $this->em->persist($appUser);
        $this->em->flush();
        if (true === $activate) {
            $this->activate($appUser, $sendMail);
        }
        if (true !== $activate && $sendMail) {
            $this->requestActivation($appUser);
        }
        $this->em->flush();
        $id = $appUser->getId();
        $this->logger->info("Created user $id/$username/$email.");

        return $appUser;
    }

    public function getNewRandomUsername(): string
    {
        try {
            return 'user'.random_int(1, 9999);
        } catch (Exception) {
            return 'user'.time();
        }
    }

    public function getRepository(): AppUserRepository
    {
        return $this->appUserRepository;
    }

    public function getDefaultAppUserType(): ?AppUserType
    {
        return $this->appUserTypeService->getRepository()->findBy(['slug' => 'attendee'], [], 1)[0] ?? null;
    }

    /**
     * @param  AppUser  $appUser
     * @param  bool  $sendConfirmation
     *
     * @throws InvalidTypeException|NotFoundException
     */
    public function activate(AppUser $appUser, bool $sendConfirmation = true): void
    {
        try {
            if (empty($appUser->getPassword())) {
                $isRandom = empty($appUser->getPlainPassword());
                $appUser->setPlainPassword($isRandom ? StringUtils::generatePassword() : $appUser->getPlainPassword(), $this->encoder, !$isRandom);
            }
            $appUser->activate();
            if ($sendConfirmation) {
                $this->appUserMailService->sendAppUserMail($appUser, self::ACTIVATION);
            }
            $this->em->persist($appUser);
            $this->em->flush();
            $this->logger->info('Successfully activated user ('.$appUser->getId().').');
        } catch (OswisException $exception) {
            $this->logger->error('User ('.$appUser->getId().') activation FAILED. '.$exception->getMessage());
        }
    }

    /**
     * @param  AppUser|null  $appUser
     *
     * @throws InvalidTypeException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     * @throws UserNotFoundException
     */
    public function requestActivation(?AppUser $appUser): void
    {
        try {
            if (null === $appUser) {
                throw new UserNotFoundException();
            }
            $appUserToken = $this->appUserTokenService->create($appUser, AppUserToken::TYPE_ACTIVATION, false);
            $this->appUserMailService->sendAppUserMail($appUser, self::ACTIVATION_REQUEST, $appUserToken);
            $this->em->persist($appUser);
            $this->em->flush();
            $this->logger->info('Created and sent activation request for user '.$appUser->getId().'.');
        } catch (OswisException | InvalidTypeException $exception) {
            $this->logger->error('User ('.$appUser->getId().') activation request FAILED. '.$exception->getMessage());
            throw $exception;
        }
    }

    public function alreadyExists(string $mail): bool
    {
        try {
            return (bool)$this->findExisting($mail);
        } catch (UserNotUniqueException) {
            return true;
        }
    }

    /**
     * @param  string  $mail
     *
     * @return AppUser|null
     * @throws UserNotUniqueException
     */
    public function findExisting(string $mail): ?AppUser
    {
        return $this->getRepository()->findOneByUsernameOrMail($mail, false);
    }

    /**
     * @param  AppUser|null  $appUser
     * @param  bool  $sendConfirmation
     *
     * @throws InvalidTypeException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     * @throws UserNotFoundException
     */
    public function requestPasswordChange(?AppUser $appUser, bool $sendConfirmation): void
    {
        try {
            if (null === $appUser) {
                throw new UserNotFoundException();
            }
            $appUserToken = $this->appUserTokenService->create($appUser, AppUserToken::TYPE_PASSWORD_CHANGE, false);
            if ($sendConfirmation) {
                $this->appUserMailService->sendAppUserMail($appUser, self::PASSWORD_CHANGE_REQUEST, $appUserToken);
            }
            $this->em->persist($appUser);
            $this->em->flush();
            $andSent = $sendConfirmation ? ' and sent' : '';
            $this->logger->info("Created $andSent password change request for user ".$appUser->getId().'.');
        } catch (OswisException | InvalidTypeException $exception) {
            $this->logger->error('User ('.$appUser->getId().') password change request FAILED. '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @param  string  $token
     * @param  int  $appUserId
     *
     * @return \OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserToken
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\TokenInvalidException
     */
    public function getVerifiedToken(string $token, int $appUserId): AppUserToken
    {
        if (null === ($appUserToken = $this->getToken($token, $appUserId))) {
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
     * @param  string  $token
     * @param  int  $appUserId
     * @param  string|null  $newPassword
     *
     * @throws InvalidTypeException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     * @throws TokenInvalidException
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
            $this->em->persist($appUserToken);
            $this->em->flush();
            if (AppUserToken::TYPE_ACTIVATION === $type) {
                $this->activate($appUserToken->getAppUser());
            }
            if (AppUserToken::TYPE_PASSWORD_CHANGE === $type) {
                $this->changePassword($appUserToken->getAppUser(), $newPassword, true);
            }
            throw new TokenInvalidException('neznámý typ tokenu', $token);
        } catch (OswisException | TokenInvalidException | InvalidTypeException $exception) {
            $this->logger->error('Problem occurred when processing app user token. '.$exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @param  AppUser  $appUser
     * @param  string|null  $password
     * @param  bool  $sendConfirmation
     *
     * @throws InvalidTypeException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     */
    public function changePassword(AppUser $appUser, ?string $password, bool $sendConfirmation): void
    {
        try {
            $isRandom = empty($password);
            $appUser->setPlainPassword($password ?? StringUtils::generatePassword(), $this->encoder, !$isRandom);
            if ($sendConfirmation) {
                $this->appUserMailService->sendAppUserMail($appUser, self::PASSWORD_CHANGE);
            }
            $this->em->persist($appUser);
            $this->em->flush();
            $this->logger->info('Successfully changed password for user ('.$appUser->getId().').');
        } catch (OswisException | InvalidTypeException $exception) {
            $this->logger->error('Password change for user ('.$appUser->getId().') FAILED. '.$exception->getMessage());
            throw $exception;
        }
    }
}
