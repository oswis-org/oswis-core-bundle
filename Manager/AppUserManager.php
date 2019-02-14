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
     * @return AppUser
     * @throws \Exception
     */
    final public function create(
        ?string $fullName,
        AppUserType $appUserType,
        string $username = null,
        string $password = null,
        string $email = null
    ): AppUser {
        $em = $this->em;

        $appUserRoleRepo = $em->getRepository(AppUser::class);
        $user = $appUserRoleRepo->findOneBy(['username' => $username]);
        if ($user) {
            $this->logger->info('Skipped existing user: '.$user->getUsername().'.');

            return $user;
        }

        $username = $username ?? 'user'.\random_int(1, 9999);
        $password = $password ?? $username.'pass9';
        $email = $email ?? $username.'@jakubzak.eu';
        $user = new AppUser($fullName, $username, $email, null, null, $password);
        $user->setPassword($this->encoder->encodePassword($user, $password));
        $user->setAppUserType($appUserType);
        $em->persist($user);
        $em->flush();
        $infoMessage = 'Created user: '.$user->getUsername().', type: '.$appUserType->getName().'\n All roles: [';

        foreach ($user->getRoles() as $oneRole) {
            $infoMessage .= $oneRole.',';
        }
        $infoMessage .= ']';
        $this->logger->info($infoMessage);

        return $user;
    }

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
     * @return bool
     */
    final public function changePassword(AppUser $appUser, bool $sendConfirmation = true, string $password = null): bool
    {
        try {
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

            return false;
        }
    }

    final public function requestPasswordReset(AppUser $appUser): void
    {
        $this->sendPasswordResetRequestEmail($appUser, $appUser->generatePasswordRequestToken());
    }

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

    final public function sendPasswordResetRequestEmail(AppUser $appUser, ?string $token = null): void
    {
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
}
