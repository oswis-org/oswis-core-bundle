<?php

namespace Zakjakub\OswisCoreBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Manager\AppUserManager;

class AppUserController extends AbstractController
{

    /**
     * @Route("/app-user-activation/{token}", name="zakjakub_oswis_core_app_user_activation")
     * @param string                       $token
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface       $em
     * @param \Swift_Mailer                $mailer
     * @param LoggerInterface              $logger
     * @param \Twig_Environment            $templating
     *
     * @return Response
     * @throws \LogicException
     */
    final public function appUserActivationAction(
        string $token,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em,
        \Swift_Mailer $mailer,
        LoggerInterface $logger,
        \Twig_Environment $templating
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
                        'message' => 'Token nebyl nalezen u žádné z registrací. 
                        Je možné, že již vypršena jeho platnost, zkuste se registrovat znovu se stejným e-mailem.
                        Pokud se to nepodaří, kontaktujte nás.',
                    ]
                );
            }

            \assert($appUser instanceof AppUser);

            $appUserManager = new AppUserManager($encoder, $em, $mailer, $logger, $templating);

            $appUserManager->appUserAction($appUser, 'activation', null, $token);

            $em->flush();

            return $this->render(
                '@ZakjakubOswisCore/web/pages/message.html.twig',
                [
                    'title'   => 'Účet aktivován!',
                    'message' => 'Účet byl úspěšně aktivován.',
                ]
            );
        } catch (\Exception $e) {
            $logger->notice(
                'App user activation error: '.$e->getMessage()
            );

            return $this->render(
                '@ZakjakubOswisCore/web/pages/message.html.twig',
                [
                    'title'   => 'Nastala chyba!',
                    'message' => 'Registraci se nepodařilo potvrdit. Kontaktujte nás a společně to vyřešíme.',
                ]
            );
        }
    }
}
