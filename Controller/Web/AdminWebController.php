<?php

namespace OswisOrg\OswisCoreBundle\Controller\Web;

use OswisOrg\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminWebController extends AbstractController
{
    public OswisCoreSettingsProvider $coreSettings;

    public function __construct(OswisCoreSettingsProvider $coreSettings)
    {
        $this->coreSettings = $coreSettings;
    }

    /**
     * Angular front-end administration.
     *
     * @return Response
     */
    final public function admin(): Response
    {
        return $this->render('@OswisOrgOswisCore/web/pages/admin.html.twig');
    }


    final public function adminSiteWebManifest(Request $request): Response
    {
        $response = new JsonResponse(
            [
                'lang'             => 'cs',
                'dir'              => 'ltr',
                'name'             => $this->coreSettings->getApp()['name'],
                'short_name'       => $this->coreSettings->getApp()['name_short'],
                'start_url'        => '.',
                'display'          => 'fullscreen',
                'description'      => $this->coreSettings->getWeb()['description'],
                'background_color' => '#FAFAFA',
                'theme_color'      => $this->coreSettings->getWeb()['color'],
                'icons'            => [
                    [
                        'src'   => $request->getUriForPath('/bundles/oswisorgoswiscore/favicons/apple-touch-icon.png'),
                        'sizes' => '180x180',
                        'type'  => 'image/png',
                    ],
                    [
                        'src'   => $request->getUriForPath('/bundles/oswisorgoswiscore/favicons/favicon-32x32.png'),
                        'sizes' => '32x32',
                        'type'  => 'image/png',
                    ],
                    [
                        'src'   => $request->getUriForPath('/bundles/oswisorgoswiscore/favicons/favicon-194x194.png'),
                        'sizes' => '194x194',
                        'type'  => 'image/png',
                    ],
                    [
                        'src'   => $request->getUriForPath('/bundles/oswisorgoswiscore/favicons/android-chrome-192x192.png'),
                        'sizes' => '192x192',
                        'type'  => 'image/png',
                    ],
                    [
                        'src'   => $request->getUriForPath('/bundles/oswisorgoswiscore/favicons/favicon-16x16.png'),
                        'sizes' => '16x16',
                        'type'  => 'image/png',
                    ],
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
            ]
        );
        $response->headers->set('Content-Type', 'application/manifest+json');

        return $response;
    }

}
