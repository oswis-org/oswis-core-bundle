<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Controller\WebAdmin;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class WebSecurityController extends AbstractController
{
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render(
            '@OswisOrgOswisCore/web_admin/login.html.twig',
            [
                'last_username' => $authenticationUtils->getLastUsername(),
                'error'         => $authenticationUtils->getLastAuthenticationError(),
            ]
        );
    }

    /**
     * @Route("/web_admin/logout", name="web_logout")
     * @throws LogicException
     */
    final public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
