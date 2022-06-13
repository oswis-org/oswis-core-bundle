<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\Web;

use OswisOrg\OswisCoreBundle\Service\Web\SiteMapService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SiteMapWebController extends AbstractController
{
    public function __construct(protected SiteMapService $siteMapService)
    {
    }

    public function showSitemapXml(): Response
    {
        return $this->renderXml($this->render('@OswisOrgOswisCore/web/sitemap.xml.twig', ['items' => $this->siteMapService->getItems()]));
    }

    public function renderXml(Response $response): Response
    {
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');

        return $response;
    }

    public function showSitemapXsl(): Response
    {
        return $this->renderXml($this->render('@OswisOrgOswisCore/web/sitemap.xsl.twig'));
    }

    public function sitemapRedirect(): Response
    {
        return $this->redirectToRoute('oswis_org_oswis_core_sitemap_xml');
    }
}
