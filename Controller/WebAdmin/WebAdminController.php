<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\WebAdmin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_MANAGER')]
final class WebAdminController extends AbstractController
{
    public function showAdminHomepage(): Response
    {
        return $this->render('@OswisOrgOswisCore/web_admin/web-admin-homepage.html.twig');
    }
}
