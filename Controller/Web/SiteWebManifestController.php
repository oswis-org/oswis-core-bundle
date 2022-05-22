<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\Web;

use OswisOrg\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteWebManifestController extends AbstractController
{
    public OswisCoreSettingsProvider $coreSettings;

    public function __construct(OswisCoreSettingsProvider $coreSettings)
    {
        $this->coreSettings = $coreSettings;
    }

    public function showBrowserConfigXml(): Response
    {
        $response = $this->render('@OswisOrgOswisCore/web/browserconfig.xml.twig');
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');

        return $response;
    }

    public function showRobotsTxt(): Response
    {
        $response = $this->render('@OswisOrgOswisCore/web/robots.txt.twig');
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }

    public function showSiteWebManifest(Request $request): Response
    {
        $response = new JsonResponse([
            'lang'             => 'cs',
            'dir'              => 'ltr',
            'name'             => $this->coreSettings->getWeb()['name'],
            'short_name'       => $this->coreSettings->getWeb()['name_short'],
            'start_url'        => '.',
            'display'          => 'standalone',
            'description'      => $this->coreSettings->getWeb()['description'],
            'background_color' => '#FAFAFA',
            'theme_color'      => $this->coreSettings->getWeb()['color'],
            'icons'            => [
                [
                    'src'   => $request->getUriForPath('/bundles/oswisorgoswiscore/favicons/android-chrome-192x192.png'),
                    'sizes' => '192x192',
                    'type'  => 'image/png',
                ],
                [
                    'src'   => $request->getUriForPath('/bundles/oswisorgoswiscore/favicons/android-chrome-512x512.png'),
                    'sizes' => '512x512',
                    'type'  => 'image/png',
                ],
            ],
        ]);
        $response->headers->set('Content-Type', 'application/manifest+json');

        return $response;
    }
}
