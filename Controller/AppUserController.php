<?php

namespace Zakjakub\OswisCoreBundle\Controller;

use Exception;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\HttpFoundation\Response;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Manager\AppUserManager;
use Zakjakub\OswisCoreBundle\Service\EmailSender;
use function assert;

class AppUserController extends AbstractController
{

    /**
     * @param string             $token
     * @param ContainerInterface $container
     * @param EmailSender        $emailSender
     *
     * @return Response
     * @throws LogicException
     * @throws ServiceCircularReferenceException
     */
    final public function appUserActivationAction(
        string $token,
        ContainerInterface $container,
        EmailSender $emailSender
    ): Response {
        try {
            $em = $container->get('doctrine.orm.entity_manager.abstract');
            $encoder = $container->get('security.password_encoder');
            $logger = $container->get('monolog.logger_prototype');
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

            assert($appUser instanceof AppUser);

            $appUserManager = new AppUserManager($encoder, $em, $logger, $emailSender);

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
            $logger = $container->get('monolog.logger_prototype');

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
