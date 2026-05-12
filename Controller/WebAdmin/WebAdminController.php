<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\WebAdmin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WebAdminController extends AbstractController
{
    #[Route(path: '/web_admin/homepage', name: 'web_admin_homepage')]
    public function showAdminHomepage(): Response
    {
        return $this->render('@OswisOrgOswisCore/web_admin/web-admin-homepage.html.twig');
    }
}
