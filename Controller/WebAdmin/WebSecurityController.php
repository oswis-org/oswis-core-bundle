<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\WebAdmin;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class WebSecurityController extends AbstractController
{
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('@OswisOrgOswisCore/web_admin/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'         => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * Logout is handled by the Symfony security firewall (`logout:` config) —
     * the request never reaches this method. The route exists purely to give
     * the firewall something to match against; if you see this exception you
     * are missing the firewall's `logout` entry.
     */
    public function logout(): never
    {
        throw new LogicException('Logout is handled by the firewall; this controller method should never be called.');
    }
}
