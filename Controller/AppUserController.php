<?php

namespace Zakjakub\OswisCoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use LogicException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Manager\AppUserManager;
use Zakjakub\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use function assert;

class AppUserController extends AbstractController
{
    /**
     * @param string                       $token
     * @param EntityManagerInterface       $em
     * @param UserPasswordEncoderInterface $encoder
     * @param LoggerInterface              $logger
     * @param MailerInterface              $newMailer
     *
     * @param OswisCoreSettingsProvider    $oswisCoreSettings
     *
     * @return Response
     * @throws LogicException
     */
    final public function appUserActivationAction(
        string $token,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder,
        LoggerInterface $logger,
        MailerInterface $newMailer,
        OswisCoreSettingsProvider $oswisCoreSettings
    ): Response {
        try {
            if (!$token) {
                return $this->render(
                    '@ZakjakubOswisCore/web/pages/message.html.twig',
                    [
                        'title'   => 'Token nezadán',
                        'message' => 'Nebyl zadán token. 
                        Zkuste odkaz otevřít znovu nebo jej zkopírovat celý do adresního řádku prohlížeče.
                        Pokud se to nepodaří, kontaktujte nás a společně to vyřešíme.',
                    ]
                );
            }

            $appUser = $this->getDoctrine()
                ->getRepository(AppUser::class)
                ->findOneBy(['accountActivationRequestToken' => $token]);

            if (!$appUser) {
                return $this->render(
                    '@ZakjakubOswisCore/web/pages/message.html.twig',
                    [
                        'title'   => 'Token nenalezen',
                        'message' => 'Token nebyl nalezen u žádného z uživatelů. 
                        Je možné, že již vypršena jeho platnost, zkuste se registrovat znovu se stejným e-mailem.
                        Pokud se to nepodaří, kontaktujte nás.',
                    ]
                );
            }

            assert($appUser instanceof AppUser);
            $appUserManager = new AppUserManager($encoder, $em, $logger, $newMailer, $oswisCoreSettings);
            $appUserManager->appUserAction($appUser, 'activation', null, $token);
            $em->flush();

            return $this->render(
                '@ZakjakubOswisCore/web/pages/message.html.twig',
                [
                    'title'   => 'Účet aktivován!',
                    'message' => 'Účet byl úspěšně aktivován.',
                ]
            );
        } catch (Exception $e) {
            $logger->notice(
                'App user activation error: '.$e->getMessage()
            );

            return $this->render(
                '@ZakjakubOswisCore/web/pages/message.html.twig',
                [
                    'title'   => 'Nastala chyba!',
                    'message' => 'Uživatele se nepodařilo potvrdit. Kontaktujte nás a společně to vyřešíme.',
                ]
            );
        }
    }
}
