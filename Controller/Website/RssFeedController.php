<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class RssFeedController extends AbstractController
{
    public function showRss(): Response
    {
        $response = $this->render('@OswisOrgOswisCore/web/pages/rss.xml.twig');
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');

        return $response;
    }
}
