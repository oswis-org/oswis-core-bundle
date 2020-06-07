<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use LogicException;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserRole;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserType;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
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

    public function __construct(
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        MailerInterface $mailer,
        OswisCoreSettingsProvider $oswisCoreSettings,
        AppUserTypeService $appUserTypeService,
        AppUserRoleService $appUserRoleService
    ) {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->oswisCoreSettings = $oswisCoreSettings;
        $this->appUserTypeService = $appUserTypeService;
        $this->appUserRoleService = $appUserRoleService;
    }

    /**
     * @throws OswisException
     * @throws InvalidArgumentException
     */
    public function registerRoot(): void
    {
        $role = $this->appUserRoleService->create(
            new AppUserRole(
                new Nameable('Superuživatel', 'Root', null, null, 'root'), 'ROOT'
            )
        );
        $type = $this->appUserTypeService->create(
            new AppUserType(
                new Nameable('Root', null, null, null, 'root'), $role, true
            )
        );
        $this->create(
            $this->oswisCoreSettings->getAdmin()['name'] ?? $this->oswisCoreSettings->getEmail()['name'],
            $type,
            'admin',
            null,
            $this->oswisCoreSettings->getAdmin()['email'] ?? $this->oswisCoreSettings->getEmail()['email'],
            null,
            true,
            false
        );
    }

    /**
     * Create and save new user of application.
     *
     * @param bool|null $activate
     *
     * @throws OswisException
     * @todo Refactor: AppUser instance instead of single arguments.
     */
    public function create(
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
        $appUser = $this->getRepository()->findOneBy(['email' => $email]) ?? $this->getRepository()->findOneBy(['username' => $username]);
        if (null !== $appUser && !$errorWhenExist) {
            $this->logger->notice('Skipped existing user '.$appUser->getUsername().' '.$appUser->getEmail().'.');

            return $appUser;
        }
        if (null !== $appUser && $errorWhenExist) {
            throw new UserNotUniqueException('Uživatel '.$appUser->getUsername().' již existuje.');
        }
        $appUser = new AppUser($fullName, $username, $email, null);
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
        $this->logger->info('[OK] Created user '.$appUser->getUsername().', type: '.($appUserType ? $appUserType->getName() : null));

        return $appUser;
    }

    public function getRepository(): AppUserRepository
    {
        $repository = $this->em->getRepository(AppUser::class);
        assert($repository instanceof AppUserRepository);

        return $repository;
    }

    /**
     * @throws OswisException
     */
    public function appUserAction(
        ?AppUser $appUser,
        string $type,
        ?string $password = null,
        ?string $token = null,
        ?bool $sendConfirmation = true,
        ?bool $withoutToken = false
    ): void {
        try {
            if (self::PASSWORD_CHANGE_REQUEST === $type) { // Create token for password reset/change and send it to user by e-mail.
                $this->passwordChangeRequest($appUser, $sendConfirmation);
            } elseif (self::PASSWORD_CHANGE === $type) { // Check token for password reset/change and change password for user.
                $this->passwordChange($appUser, $password, $token, $sendConfirmation, $withoutToken);
            } elseif (self::ACTIVATION_REQUEST === $type) { // Generate token for account activation and send it to user by e-mail.
                $this->activationRequest($appUser);
            } elseif (self::ACTIVATION === $type) { // Check activation token and activate account.
                $this->activation($appUser, $password, $token, $sendConfirmation, $withoutToken);
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

    /**
     * @param AppUser|null $appUser
     * @param bool         $sendConfirmation
     *
     * @throws OswisException
     */
    private function passwordChangeRequest(?AppUser $appUser, bool $sendConfirmation): void
    {
        if (null === $appUser) {
            throw new UserNotFoundException();
        }
        $token = $appUser->generatePasswordRequestToken();
        if ($sendConfirmation) {
            $this->sendPasswordEmail($appUser, self::PASSWORD_CHANGE_REQUEST, $token);
        }
        $this->logger->info('[OK] Created password change request for app user '.$appUser->getId().'');
    }

    /**
     * @throws OswisException
     */
    public function sendPasswordEmail(AppUser $appUser, string $type, ?string $token = null, string $password = null): void
    {
        try {
            if (self::PASSWORD_CHANGE === $type) { // Send e-mail about password change. Include password if present (it means that it's generated randomly).
                $title = 'Heslo změněno';
                $token = null;
            } elseif (self::PASSWORD_CHANGE_REQUEST === $type) { // Send e-mail about password reset request. Include token for change.
                $title = 'Požadavek na změnu hesla';
                $password = null;
            } else {
                throw new NotImplementedException($type, 'u změny hesla');
            }
            $data = [
                'type'     => $type,
                'appUser'  => $appUser,
                'token'    => $token,
                'password' => $password,
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
     * @param string|null  $password
     * @param string|null  $token
     * @param bool         $sendConfirmation
     * @param bool         $withoutToken
     *
     * @throws OswisException
     */
    private function passwordChange(?AppUser $appUser, ?string $password, ?string $token, bool $sendConfirmation, bool $withoutToken): void
    {
        $appUser ??= $this->getRepository()->findOneBy(['passwordResetRequestToken' => $token]);
        if (null === $appUser) {
            throw new UserNotFoundException();
        }
        if (!$withoutToken && !$token) {
            throw new OswisException('Token pro změnu hesla nebyl zadán.');
        }
        if (!$withoutToken && !$appUser->checkAndDestroyPasswordResetRequestToken($token)) {
            throw new OswisException('Token pro změnu hesla neexistuje nebo vypršela jeho platnost.');
        }
        $random = empty($password);
        $password ??= StringUtils::generatePassword();
        $appUser->setPassword($this->encoder->encodePassword($appUser, $password));
        if ($sendConfirmation) {
            $this->sendPasswordEmail($appUser, self::PASSWORD_CHANGE, null, $random ? $password : null);
        }
        $this->logger->info('[OK] Password changed for app user '.$appUser->getId().'');
    }

    /**
     * @param AppUser|null $appUser
     *
     * @throws OswisException
     */
    private function activationRequest(?AppUser $appUser): void
    {
        if (null === $appUser) {
            throw new UserNotFoundException();
        }
        $this->sendAppUserEmail($appUser, self::ACTIVATION_REQUEST, $appUser->generateActivationRequestToken());
        $this->logger->info('[OK] Created activation request for app user '.$appUser->getId().'');
    }

    /**
     * @throws OswisException
     */
    public function sendAppUserEmail(AppUser $appUser, string $type, ?string $token = null, ?string $password = null): void
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
                'appUser'  => $appUser,
                'type'     => $type,
                'token'    => $token,
                'password' => $password,
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

    /**
     * @param AppUser|null $appUser
     * @param string|null  $password
     * @param string|null  $token
     * @param bool         $sendConfirmation
     * @param bool         $withoutToken
     *
     * @throws OswisException
     */
    private function activation(?AppUser $appUser, ?string $password, ?string $token, bool $sendConfirmation, bool $withoutToken): void
    {
        if (!$withoutToken && !$token) {
            throw new OswisException('Token pro aktivaci účtu nebyl zadán. Otevřete odkaz znovu.');
        }
        $appUser ??= $this->getRepository()->findOneByToken($token);
        if (!$withoutToken && (!($appUser instanceof AppUser) || !$appUser->activateByToken($token))) {
            throw new OswisException('Token pro aktivaci účtu není platný (neexistuje nebo vypršela jeho platnost).');
        }
        $random = empty($password);
        $password ??= StringUtils::generatePassword();
        $appUser->setPassword($this->encoder->encodePassword($appUser, $password));
        if ($sendConfirmation) {
            $this->sendAppUserEmail($appUser, self::ACTIVATION, $token, $random ? $password : null);
        }
        $this->em->persist($appUser);
        $this->logger->info('[OK] App user '.$appUser->getId().' successfully activated.');
    }
}
