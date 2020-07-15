<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Controller\Website;

use OswisOrg\OswisCoreBundle\Service\SiteMapService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SiteMapWebController extends AbstractController
{
    protected SiteMapService $siteMapService;

    public function __construct(SiteMapService $siteMapService)
    {
        $this->siteMapService = $siteMapService;
    }

    public function showSitemapXml(): Response
    {
        return $this->renderXml($this->render('@OswisOrgOswisCore/web/pages/sitemap.xml.twig', ['items' => $this->siteMapService->getItems()]));
    }

    public function renderXml(Response $response): Response
    {
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');

        return $response;
    }

    public function showSitemapXsl(): Response
    {
        return $this->renderXml($this->render('@OswisOrgOswisCore/web/pages/sitemap.xsl.twig'));
    }

    public function sitemapRedirect(): Response
    {
        return $this->redirectToRoute('oswis_org_oswis_core_sitemap_xml');
    }
}
