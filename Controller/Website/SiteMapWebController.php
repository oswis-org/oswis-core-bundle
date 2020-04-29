<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SiteMapWebController extends AbstractController
{
    final public function showSitemapXml(): Response
    {
        $data = [
            'items' => [
                [
                    'path'            => $this->generateUrl('oswis_org_oswis_core_homepage_action'),
                    'changeFrequency' => 'daily',
                    'changed'         => date_create(),
                    'priority'        => 1.000,
                ],
                ['path' => $this->generateUrl('oswis_org_oswis_core_gdpr_action'), 'changeFrequency' => 'weekly'],
                ['path' => $this->generateUrl('oswis_org_oswis_core_robots_action'), 'changeFrequency' => 'weekly'],
                ['path' => $this->generateUrl('oswis_org_oswis_core_web_portal'), 'changeFrequency' => 'weekly'],
            ],
        ];
        $response = $this->render('@OswisOrgOswisCore/web/pages/sitemap.xml.twig', $data);
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');

        return $response;
    }

    public function showSitemapXsl(): Response
    {
        return $this->render('@OswisOrgOswisCore/web/pages/sitemap.xsl.twig');
    }

    public function showSitemapIndex(): Response
    {
        return $this->render('@OswisOrgOswisCore/web/pages/sitemap-index.xml.twig');
    }

    public function sitemapRedirect(): Response
    {
        return $this->redirectToRoute('oswis_org_oswis_core_sitemap_index_action');
    }
}
