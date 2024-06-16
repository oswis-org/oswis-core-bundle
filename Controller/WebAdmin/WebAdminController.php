<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\WebAdmin;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

class WebAdminController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route(path: '/web_admin/homepage', name: 'web_admin_homepage')]
    final public function showAdminHomepage(): Response
    {
        return $this->render('@OswisOrgOswisCore/web_admin/web-admin-homepage.html.twig');
    }

    final public function testSession(Session $session): Response
    {
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $session->set(''.$timestamp, $date->format("d.m.Y H:i:s"));

        return $this->render('@OswisOrgOswisCore/web_admin/sessions.html.twig');
    }
}
