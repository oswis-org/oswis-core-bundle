<?php

namespace Zakjakub\OswisCoreBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Entity\AppUserType;
use Zakjakub\OswisCoreBundle\Utils\EmailUtils;
use Zakjakub\OswisCoreBundle\Utils\StringUtils;

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
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Twig_Environment
     */
    protected $templating;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * AppUserManager constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface       $em
     * @param \Swift_Mailer                $mailer
     * @param LoggerInterface              $logger
     * @param \Twig_Environment            $templating
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em,
        \Swift_Mailer $mailer,
        LoggerInterface $logger,
        \Twig_Environment $templating
    ) {
        $this->encoder = $encoder;
        $this->em = $em;
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->templating = $templating;
    }

    /**
     * @param string|null      $fullName
     * @param AppUserType|null $appUserType
     * @param string|null      $username
     * @param string|null      $password
     * @param string|null      $email
     *
     * @param bool|null        $active
     *
     * @param bool|null        $sendMail
     *
     * @param bool|null        $errorWhenExist
     *
     * @return AppUser
     * @throws \Exception
     */
    final public function create(
        ?string $fullName,
        ?AppUserType $appUserType,
        ?string $username = null,
        ?string $password = null,
        ?string $email = null,
        ?bool $active = false,
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
            throw new \Exception('User: '.$appUser->getUsername().' already exist.');
        }

        $username = $username ?? 'user'.\random_int(1, 9999);
        $password = $password ?? $username.'pass9';
        $email = $email ?? $username.'@jakubzak.eu';
        $appUser = new AppUser($fullName, $username, $email, null, null, $password);
        if ($password) {
            $appUser->setPassword($this->encoder->encodePassword($appUser, $password));
        }
        if ($active === false) {
            $token = $appUser->generateAccountActivationRequestToken();
        }
        $appUser->setAppUserType($appUserType);
        $em->persist($appUser);
        $em->flush();
        $infoMessage = 'Created user: '.$appUser->getUsername().', type: '.$appUserType->getName().'\n All roles: [';

        foreach ($appUser->getRoles() as $oneRole) {
            $infoMessage .= $oneRole.',';
        }
        $infoMessage .= ']';
        $this->logger->info($infoMessage);

        if ($sendMail) {
            $this->sendActivationRequestEmail($appUser, $token, 'new');
        }

        return $appUser;
    }

    /**
     * @param AppUser $appUser
     * @param string  $token
     *
     * @return bool
     * @throws \Exception
     */
    final public function resetPassword(AppUser $appUser, string $token): bool
    {
        $this->logger->info('Requesting password reset (user id '.($appUser->getId() ?? '?').').');

        return $appUser->checkAndDestroyPasswordResetRequestToken($token) ? $this->changePassword($appUser) : false;
    }

    /**
     * @param AppUser     $appUser
     * @param bool        $sendConfirmation
     * @param string|null $password
     *
     * @param string|null $token
     *
     * @param bool|null   $withoutToken
     *
     * @return bool
     * @throws \Exception
     */
    final public function changePassword(
        AppUser $appUser,
        bool $sendConfirmation = true,
        string $password = null,
        ?string $token = null,
        ?bool $withoutToken = false
    ): bool {
        if (!$withoutToken && !$token) {
            throw new \Exception('Token nenalezen.');
        }
        try {
            if (!$withoutToken && !$appUser->checkAndDestroyPasswordResetRequestToken($token)) {
                throw new \Exception('Špatný token.');
            }
            $random = $password ? false : true;
            $password = $password ?? StringUtils::generatePassword();
            $appUser->setPassword($this->encoder->encodePassword($appUser, $password));
            if ($sendConfirmation) {
                $this->sendPasswordChangedEmail($appUser, $random ? $password : null);
            }
            $this->em->persist($appUser);
            $this->em->flush();

            return true;
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            throw new \Exception('Nastal problém při změně hesla ('.$e->getMessage().').');
        }
    }

    /**
     * @param AppUser     $appUser
     * @param bool        $sendConfirmation
     * @param string|null $password
     * @param string|null $token
     * @param bool|null   $withoutToken
     *
     * @return bool
     * @throws \Exception
     */
    final public function activateAccount(
        AppUser $appUser,
        bool $sendConfirmation = true,
        string $password = null,
        ?string $token = null,
        ?bool $withoutToken = false
    ): bool {
        if (!$withoutToken && !$token) {
            throw new \Exception('Token nenalezen.');
        }
        try {
            if (!$withoutToken && !$appUser->checkAndDestroyAccountActivationRequestToken($token)) {
                throw new \Exception('Špatný token.');
            }
            $random = $password ? false : true;
            $password = $password ?? StringUtils::generatePassword();
            $appUser->setPassword($this->encoder->encodePassword($appUser, $password));
            if ($sendConfirmation) {
                $this->sendActivationEmail($appUser, $random ? $password : null);
            }
            $this->em->persist($appUser);
            $this->em->flush();

            return true;
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            throw new \Exception('Nastal problém při aktivaci účtu ('.$e->getMessage().').');
        }
    }

    /**
     * @param AppUser $appUser
     *
     * @throws \Exception
     */
    final public function requestPasswordReset(AppUser $appUser): void
    {
        $this->sendPasswordResetRequestEmail($appUser, $appUser->generatePasswordRequestToken());
    }

    /**
     * @param AppUser     $appUser
     * @param string|null $password
     *
     * @throws \Exception
     */
    final public function sendPasswordChangedEmail(AppUser $appUser, ?string $password = null): void
    {
        try {
            if (!$appUser) {
                throw new \Exception('Uživatel nenalezen.');
            }
            if (!$password) {
                throw new \Exception('Heslo nenalezeno.');
            }

            $em = $this->em;

            $title = 'Změna hesla';

            $message = new \Swift_Message(EmailUtils::mime_header_encode($title));

            $message->setTo(array($appUser->getFullName() ?? $appUser->getUsername() => $appUser->getEmail()))
                ->setCharset('UTF-8');

            $message->setBody(
                $this->templating->render(
                    '@ZakjakubOswisCore/e-mail/password.html.twig',
                    array(
                        'appUser'     => $appUser,
                        'token'       => null,
                        'newPassword' => $password,
                    )
                ),
                'text/html'
            );

            $message->addPart(
                $this->templating->render(
                    '@ZakjakubOswisCore/e-mail/password.txt.twig',
                    array(
                        'appUser'     => $appUser,
                        'token'       => null,
                        'newPassword' => $password,
                    )
                ),
                'text/plain'
            );

            if ($this->mailer->send($message)) {
                return;
            }

            throw new \Exception();
        } catch (\Exception $e) {
            throw new \Exception('Problém s odesláním zprávy o změně hesla.  '.$e->getMessage());
        }

    }

    /**
     * @param AppUser     $appUser
     * @param string|null $token
     *
     * @throws \Exception
     */
    final public function sendPasswordResetRequestEmail(
        AppUser $appUser,
        ?string $token = null
    ): void {
        try {

            if (!$appUser) {
                throw new \Exception('Uživatel nenalezen.');
            }
            if (!$token) {
                throw new \Exception('Token nenalezen.');
            }

            $em = $this->em;

            $title = 'Reset hesla';

            $message = new \Swift_Message(EmailUtils::mime_header_encode($title));

            $message->setTo(array($appUser->getFullName() ?? $appUser->getUsername() => $appUser->getEmail()))
                ->setCharset('UTF-8');

            $message->setBody(
                $this->templating->render(
                    '@ZakjakubOswisCore/e-mail/password.html.twig',
                    array(
                        'appUser'     => $appUser,
                        'token'       => $token,
                        'newPassword' => null,
                    )
                ),
                'text/html'
            );

            $message->addPart(
                $this->templating->render(
                    '@ZakjakubOswisCore/e-mail/password.txt.twig',
                    array(
                        'appUser'     => $appUser,
                        'token'       => $token,
                        'newPassword' => null,
                    )
                ),
                'text/plain'
            );

            if ($this->mailer->send($message)) {
                return;
            }

            throw new \Exception();
        } catch (\Exception $e) {
            throw new \Exception('Problém s odesláním zprávy o resetu hesla.  '.$e->getMessage());
        }
    }

    /**
     * @param AppUser $appUser
     *
     * @throws \Exception
     */
    final public function requestUserActivation(AppUser $appUser): void
    {
        $token = $appUser->generateAccountActivationRequestToken();
        $this->sendActivationRequestEmail($appUser, $token, 'new');
    }

    /**
     * @param AppUser     $appUser
     * @param string|null $token
     * @param string|null $type
     *
     * @throws \Exception
     */
    final public function sendActivationRequestEmail(
        AppUser $appUser,
        ?string $token = null,
        ?string $type = 'change'
    ): void {
        try {

            if (!$appUser) {
                throw new \Exception('Uživatel nenalezen.');
            }

            $em = $this->em;

            $title = 'Změna v uživatelském účtu';
            if ($type === 'new') {
                $title = 'Vytvořen nový uživatelský účet';
            }

            $message = new \Swift_Message(EmailUtils::mime_header_encode($title));

            $message
                ->setTo([$appUser->getEmail() ?? '' => $appUser->getFullName() ?? $appUser->getUsername() ?? ''])
                ->setFrom(
                    array(
                        'dagmar.petrzelova@upol.cz' => EmailUtils::mime_header_encode('Mgr. Dagmar Petrželová'),
                    )
                )
                ->setSender('dagmar.petrzelova@upol.cz')
                ->setCharset('UTF-8');

            $message->setBody(
                $this->templating->render(
                    '@ZakjakubOswisCore/e-mail/app-user.html.twig',
                    array(
                        'appUser'  => $appUser,
                        'type'     => $type,
                        'token'    => $token,
                        'tokenUrl' => $token,
                        'password' => null,
                    )
                ),
                'text/html'
            );

            $message->addPart(
                $this->templating->render(
                    '@ZakjakubOswisCore/e-mail/app-user.txt.twig',
                    array(
                        'appUser'  => $appUser,
                        'type'     => $type,
                        'token'    => $token,
                        'tokenUrl' => $token,
                        'password' => null,
                    )
                ),
                'text/plain'
            );

            if ($this->mailer->send($message)) {
                return;
            }

            throw new \Exception();
        } catch (\Exception $e) {
            throw new \Exception('Problém s odesláním zprávy o změně účtu.  '.$e->getMessage());
        }
    }


    /**
     * @param AppUser     $appUser
     * @param string|null $password
     * @param string|null $token
     * @param string|null $type
     *
     * @throws \Exception
     */
    final public function sendActivationEmail(
        AppUser $appUser,
        ?string $password = null,
        ?string $token = null,
        ?string $type = 'activation'
    ): void {
        try {

            if (!$appUser) {
                throw new \Exception('Uživatel nenalezen.');
            }

            $em = $this->em;

            $title = 'Účet aktivován';

            $message = new \Swift_Message(EmailUtils::mime_header_encode($title));

            $message->setTo(array($appUser->getFullName() ?? $appUser->getUsername() => $appUser->getEmail()))
                ->setCharset('UTF-8');

            $message->setBody(
                $this->templating->render(
                    '@ZakjakubOswisCore/e-mail/app-user.html.twig',
                    array(
                        'appUser'  => $appUser,
                        'type'     => $type,
                        'token'    => $token,
                        'password' => $password,
                    )
                ),
                'text/html'
            );

            $message->addPart(
                $this->templating->render(
                    '@ZakjakubOswisCore/e-mail/app-user.txt.twig',
                    array(
                        'appUser'  => $appUser,
                        'type'     => $type,
                        'token'    => $token,
                        'password' => $password,
                    )
                ),
                'text/plain'
            );

            if ($this->mailer->send($message)) {
                return;
            }

            throw new \Exception();
        } catch (\Exception $e) {
            throw new \Exception('Problém s odesláním zprávy o aktivaci účtu.  '.$e->getMessage());
        }
    }


}
