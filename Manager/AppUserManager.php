<?php

namespace Zakjakub\OswisCoreBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Entity\AppUserType;
use Zakjakub\OswisCoreBundle\Service\EmailSender;
use Zakjakub\OswisCoreBundle\Utils\EmailUtils;
use Zakjakub\OswisCoreBundle\Utils\StringUtils;
use function random_int;

/**
 * Class AppUserManager
 * @package Zakjakub\OswisCoreBundle\Manager
 */
class AppUserManager
{

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
     * @var EmailSender
     */
    private $emailSender;

    /**
     * AppUserManager constructor.
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
        $this->encoder = $encoder;
        $this->em = $em;
        $this->logger = $logger;
        $this->emailSender = $emailSender;
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
     */
    final public function create(
        ?string $fullName,
        ?AppUserType $appUserType,
        ?string $username = null,
        ?string $password = null,
        ?string $email = null,
        ?bool $activate = false,
        ?bool $sendMail = false,
        ?bool $errorWhenExist = true
    ): AppUser {
        $em = $this->em;
        $token = null;

        $appUserRoleRepo = $em->getRepository(AppUser::class);
        $appUser = $appUserRoleRepo->findOneBy(['username' => $username]);
        if ($appUser && !$errorWhenExist) {
            $this->logger->info('Skipped existing user: '.$appUser->getUsername().'.');

            return $appUser;
        }

        if ($appUser && $errorWhenExist) {
            throw new ErrorException('User: '.$appUser->getUsername().' already exist.');
        }

        $username = $username ?? 'user'.random_int(1, 9999);
        $email = $email ?? $username.'@jakubzak.eu';
        $appUser = new AppUser($fullName, $username, $email, null, null);
        $appUser->setAppUserType($appUserType);
        if ($activate) {
            $password = $password ?? StringUtils::generatePassword();
            $appUser->setPassword($this->encoder->encodePassword($appUser, $password));
            $this->appUserAction($appUser, 'activation', $password, null, $sendMail, true);
        } else {
            $this->appUserAction($appUser, 'activation-request', null, null, $sendMail);
        }
        $em->persist($appUser);
        $em->flush();
        $infoMessage = 'Created user: '.$appUser->getUsername().', type: '.($appUserType ? $appUserType->getName() : null);
        $this->logger->info($infoMessage);

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
            if ($type === 'reset-request') {
                // Create token for password reset/change and send it to user by e-mail.
                $token = $appUser->generatePasswordRequestToken();
                if ($sendConfirmation) {
                    $this->sendPasswordEmail($appUser, 'reset-request', $token);
                }
            } elseif ($type === 'reset') {
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
                    $this->sendPasswordEmail($appUser, 'reset', null, $random ? $password : null);
                }
            } elseif ($type === 'activation-request') {
                // Generate token for account activation and send it to user by e-mail.
                $this->sendAppUserEmail($appUser, 'activation-request', $appUser->generateAccountActivationRequestToken());
            } elseif ($type === 'activation') {
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
                    $this->sendAppUserEmail($appUser, 'activation', $token, $random ? $password : null);
                }
            } else {
                // Type is not recognized.
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

            if ('reset' === $type) {
                // Send e-mail about password change. Include password if present (it means that it's generated randomly).
                $title = 'Heslo změněno';
                $token = null;
            } elseif ('reset-request' === $type) {
                // Send e-mail about password reset request. Include token for change.
                $title = 'Požadavek na změnu hesla';
                $password = null;
            } else {
                throw new InvalidArgumentException('Akce "'.$type.'" není u změny hesla implementována.');
            }

            $message = $this->emailSender->getPreparedMessage(
                [$appUser->getEmail() => EmailUtils::mime_header_encode($appUser->getFullName() ?? $appUser->getUsername())],
                EmailUtils::mime_header_encode($title)
            );

            $data = array(
                'appUser'  => $appUser,
                'token'    => $token,
                'password' => $password,
            );

            $this->emailSender->sendMessage($message, '@ZakjakubOswisCore/e-mail/password', $data);

            throw new ErrorException('Problém s odesláním zprávy o změně hesla.');
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new ErrorException('Problém s odesláním zprávy o změně hesla:  '.$e->getMessage());
        }
    }

    /**
     * @param AppUser     $appUser
     * @param string      $type
     * @param string|null $token
     * @param string|null $password
     *
     * @throws ErrorException
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

            if ('activation-request' === $type) {
                // Send e-mail about activation request. Include token for activation.
                $title = 'Aktivace uživatelského účtu';
            } elseif ('activation' === $type) {
                // Send e-mail about account activation. Include password if present (it means that it's generated randomly).
                $title = 'Účet byl aktivován';
            } else {
                throw new InvalidArgumentException('Akce "'.$type.'" není u uživatelských účtů implementována.');
            }

            $message = $this->emailSender->getPreparedMessage(
                [$appUser->getEmail() ?? '' => EmailUtils::mime_header_encode($appUser->getFullName() ?? $appUser->getUsername() ?? '')],
                EmailUtils::mime_header_encode($title)
            );

            $data = array(
                'appUser'  => $appUser,
                'type'     => $type,
                'token'    => $token,
                'password' => $password,
            );

            $this->emailSender->sendMessage($message, '@ZakjakubOswisCore/e-mail/app-user', $data);

            throw new ErrorException('Problém s odesláním zprávy o změně účtu.');
        } catch (Exception $e) {
            throw new ErrorException('Problém s odesláním zprávy o změně účtu:  '.$e->getMessage());
        }
    }
}
