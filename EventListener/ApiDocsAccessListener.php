<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Prod hardening: nevystavovat veřejně dokumentaci API.
 *
 * `/api/docs.{jsonld,json,html}` vrací kompletní Hydra/OpenAPI popis VŠECH
 * resourceů, jejich vlastností, typů, validací a operací (~577 kB, HTTP 200 bez
 * tokenu) = hotová mapa útočné plochy. Vlastní data chrání per-operační security
 * API Platform; dokumentaci žádný klient (Ionic ani web admin) na produkci
 * nekonzumuje (ověřeno: v mobilním klientovi 0 referencí na /api/docs).
 *
 * Symfony `access_control` tu nefunguje — `/api` běží na STATELESS JWT firewallu,
 * kde se pro anonymní request pravidlo s rolí nevynutí (na stateful `main`/web_admin
 * ano). Proto cestu blokujeme deterministicky zde, ještě před routerem, a POUZE
 * v prod env (v dev zůstává Swagger UI/ReDoc vývojáři k ruce). Vracíme 404 (ne 403),
 * aby odpověď neprozradila, že endpoint vůbec existuje.
 *
 * Umístěno v EventListener/ (ne EventSubscriber/), protože EventSubscriber/* je
 * v services.yaml globem tagovaný `kernel.view`; tenhle posluchač poslouchá
 * `kernel.request` a registruje se explicitně s `%kernel.environment%`.
 */
final readonly class ApiDocsAccessListener implements EventSubscriberInterface
{
    public function __construct(private string $environment)
    {
    }

    public static function getSubscribedEvents(): array
    {
        // Priorita 100 = ještě před RouterListenerem (32) i firewallem (8);
        // pathInfo je z requestu dostupné okamžitě.
        return [KernelEvents::REQUEST => ['onKernelRequest', 100]];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ('prod' !== $this->environment || !$event->isMainRequest()) {
            return;
        }
        // ^/api/docs následované . nebo / nebo koncem — nechytá např. /api/docstore.
        if (1 === preg_match('~^/api/docs(?:[./]|$)~', $event->getRequest()->getPathInfo())) {
            throw new NotFoundHttpException();
        }
    }
}
