<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\Web;

use OswisOrg\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PortalWebController extends AbstractController
{
    /**
     * Vstup do portálu (aplikace) — `/portal`, `/portal/overview`, `/portal/calendar/…`.
     *
     * Přesměrování na adresu aplikace **nese cestu i dotaz dál**. Dřív je zahodilo, takže odkaz
     * `…/portal/overview` (třeba z e-mailu) skončil vždycky na úvodní obrazovce. Aplikace si pak
     * nepřihlášeného návštěvníka pošle na přihlášení a po něm ho na vyžádanou stránku vrátí sama
     * (`AccessGuard` + `RequiredRouteService`), takže stačí cestu neztratit.
     */
    final public function portal(
        Request $request,
        OswisCoreSettingsProvider $oswisCoreSettings,
        ?string $foo = null,
    ): Response {
        $portalUrl = $oswisCoreSettings->getApp()['portalUrl'];
        if (!empty($portalUrl) && is_string($portalUrl)) {
            return $this->redirect(rtrim($portalUrl, '/').'/portal'.self::path($foo).self::query($request));
        }

        return $this->render('@OswisOrgOswisCore/web/pages/portal.html.twig');
    }

    /**
     * Cesta uvnitř portálu, propuštěné jen běžné znaky adresy.
     *
     * Do cizí domény se tím dostat nedá (skládá se za `portalUrl`), ale `..` a zpětná lomítka
     * do adresy nepatří — a co nepatří, to se nepropouští.
     */
    private static function path(?string $foo): string
    {
        $path = trim((string) $foo, '/');
        if ('' === $path || 1 !== preg_match('~^[A-Za-z0-9/_-]+$~', $path) || str_contains($path, '..')) {
            return '';
        }

        return '/'.$path;
    }

    private static function query(Request $request): string
    {
        $query = $request->getQueryString();

        return null === $query || '' === $query ? '' : '?'.$query;
    }
}
