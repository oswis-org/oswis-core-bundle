<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class GdprWebController extends AbstractController
{
    final public function gdprAction(): Response
    {
        return $this->render('@OswisOrgOswisCore/web/pages/gdpr.html.twig');
    }
}
