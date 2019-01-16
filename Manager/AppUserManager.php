<?php

namespace Zakjakub\OswisResourcesBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Zakjakub\OswisResourcesBundle\Entity\AppUser;
use Zakjakub\OswisResourcesBundle\Entity\AppUserType;
use Zakjakub\OswisResourcesBundle\Utils\StringUtils;

/**
 * Class AppUserManager
 * @package Zakjakub\OswisResourcesBundle\Manager
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
     * @param AppUserType|null $appUserType
     * @param string|null      $username
     * @param string|null      $password
     * @param string|null      $email
     *
     * @return AppUser
     * @throws \Exception
     */
    final public function create(
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
        $user = new AppUser($username, $email, null, null, $password);
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

    final public function sendPasswordChangedEmail(AppUser $appUser, ?string $password = null): void
    {
    }

    final public function requestPasswordReset(AppUser $appUser): void
    {
        $this->sendPasswordResetRequestEmail($appUser, $appUser->generatePasswordRequestToken());
    }

    final public function sendPasswordResetRequestEmail(AppUser $appUser, ?string $token = null): void
    {
    }
}
