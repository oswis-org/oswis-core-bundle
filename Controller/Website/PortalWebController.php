<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Controller\Website;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PortalWebController extends AbstractController
{
    /**
     * Temporary Portal placement route.
     *
     * @return Response
     * @throws LogicException
     */
    final public function portal(): Response
    {
        return $this->render('@OswisOrgOswisCore/web/pages/portal.html.twig');
    }
}
