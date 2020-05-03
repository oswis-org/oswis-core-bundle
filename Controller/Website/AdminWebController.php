<?php

namespace OswisOrg\OswisCoreBundle\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AdminWebController extends AbstractController
{
    /**
     * Angular front-end administration.
     *
     * @return Response
     */
    final public function admin(): Response
    {
        return $this->render('@OswisOrgOswisCore/web/pages/admin.html.twig');
    }
}
