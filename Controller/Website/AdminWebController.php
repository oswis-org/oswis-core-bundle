<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AdminWebController extends AbstractController
{
    /**
     * Administration frontend placement wildcard route.
     *
     * @return Response
     */
    final public function admin(): Response
    {
        return $this->render('@OswisOrgOswisCore/web/pages/admin.html.twig');
    }
}
