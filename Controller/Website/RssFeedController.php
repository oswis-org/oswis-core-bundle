<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Controller\Website;

use OswisOrg\OswisCoreBundle\Service\RssService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class RssFeedController extends AbstractController
{
    protected RssService $rssService;

    public function __construct(RssService $rssService)
    {
        $this->rssService = $rssService;
    }

    public function showRssXml(): Response
    {
        $response = $this->render('@OswisOrgOswisCore/web/pages/rss.xml.twig', ['items' => $this->rssService->getItems()]);
        $response->headers->set('Content-Type', 'application/rss+xml; charset=utf-8');

        return $response;
    }

    public function showRssCss(): Response
    {
        $response = $this->render('@OswisOrgOswisCore/web/styles/rss.css.twig');
        $response->headers->set('Content-Type', 'text/css; charset=utf-8');

        return $response;
    }

    public function rssRedirect(): Response
    {
        return $this->redirectToRoute('oswis_org_oswis_core_rss_xml');
    }
}
