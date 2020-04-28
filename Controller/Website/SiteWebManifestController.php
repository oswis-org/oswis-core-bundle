<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Controller\Website;

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

    public function browserconfig(): Response
    {
        $response = $this->render('@OswisOrgOswisCore/web/pages/browserconfig.xml.twig');
        $response->headers->set('Content-Type', 'xml');

        return $response;
    }

    public function robots(): Response
    {
        $response = $this->render('@OswisOrgOswisCore/web/pages/browserconfig.xml.twig');
        $response->headers->set('Content-Type', 'xml');

        return $response;
    }

    public function siteWebManifest(Request $request): Response
    {
        return new JsonResponse(
            [
                [
                    'lang'             => 'cs',
                    'dir'              => 'ltr',
                    'name'             => $this->coreSettings->getWeb()['name'],
                    'short_name'       => $this->coreSettings->getWeb()['short_name'],
                    'start_url'        => '.',
                    'display'          => 'fullscreen',
                    'description'      => $this->coreSettings->getWeb()['description'],
                    'background_color' => '#FFFFFF',
                    'theme_color'      => $this->coreSettings->getWeb()['color'],
                    'icons'            => [
                        [
                            'src'   => $request->getRelativeUriForPath('@OswisOrgOswisCoreBundle/Resources/public/favicons/apple-touch-icon.png'),
                            'sizes' => '180x180',
                            'type'  => 'image/png',
                        ],
                        [
                            'src'   => $request->getRelativeUriForPath('@OswisOrgOswisCoreBundle/Resources/public/favicons/favicon-32x32.png'),
                            'sizes' => '32x32',
                            'type'  => 'image/png',
                        ],
                        [
                            'src'   => $request->getRelativeUriForPath('@OswisOrgOswisCoreBundle/Resources/public/favicons/favicon-194x194.png'),
                            'sizes' => '194x194',
                            'type'  => 'image/png',
                        ],
                        [
                            'src'   => $request->getRelativeUriForPath('@OswisOrgOswisCoreBundle/Resources/public/favicons/android-chrome-192x192.png'),
                            'sizes' => '192x192',
                            'type'  => 'image/png',
                        ],
                        [
                            'src'   => $request->getRelativeUriForPath('@OswisOrgOswisCoreBundle/Resources/public/favicons/favicon-16x16.png'),
                            'sizes' => '16x16',
                            'type'  => 'image/png',
                        ],
                        [
                            'src'   => $request->getRelativeUriForPath('@OswisOrgOswisCoreBundle/Resources/public/favicons/android-chrome-192x192.png'),
                            'sizes' => '192x192',
                            'type'  => 'image/png',
                        ],
                        [
                            'src'   => $request->getRelativeUriForPath('@OswisOrgOswisCoreBundle/Resources/public/favicons/android-chrome-512x512.png'),
                            'sizes' => '512x512',
                            'type'  => 'image/png',
                        ],
                    ],
                ],
            ]
        );
    }
}
