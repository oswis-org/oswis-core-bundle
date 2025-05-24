<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\Web;

use OswisOrg\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PortalWebController extends AbstractController
{
    /**
     * Temporary Portal placement route (or redirect if app.portalUrl is set).
     *
     * @param OswisCoreSettingsProvider $oswisCoreSettings
     *
     * @return Response
     */
    final public function portal(
        OswisCoreSettingsProvider $oswisCoreSettings,
    ): Response {
        $portalUrl = $oswisCoreSettings->getApp()['portalUrl'];
        if (!empty($portalUrl) && is_string($portalUrl)) {
            return $this->redirect($portalUrl);
        }

        return $this->render('@OswisOrgOswisCore/web/pages/portal.html.twig');
    }
}
