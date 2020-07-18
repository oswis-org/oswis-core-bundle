<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Controller\Web;

use Exception;
use OswisOrg\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use OswisOrg\OswisCoreBundle\Service\AppUserDefaultsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class InternalActionsWebController extends AbstractController
{
    protected OswisCoreSettingsProvider $coreSettings;

    private AppUserDefaultsService $appUserDefaultsService;

    public function __construct(OswisCoreSettingsProvider $coreSettings, AppUserDefaultsService $appUserDefaultsService)
    {
        $this->coreSettings = $coreSettings;
        $this->appUserDefaultsService = $appUserDefaultsService;
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws AccessDeniedHttpException
     * @throws IOException
     */
    final public function clearCache(Request $request): Response
    {
        $this->checkIP($request);
        $filesystem = new Filesystem();
        $filesystem->remove('../var/cache');

        return $this->render(
            '@OswisOrgOswisCore/web/pages/message.html.twig',
            ['title' => 'OK', 'message' => 'Akce úspěšně provedena.']
        );
    }

    /**
     * @param Request $request
     *
     * @throws AccessDeniedHttpException
     */
    public function checkIP(Request $request): void
    {
        $allowedIPs = $this->coreSettings->getAdminIPs();
        if (!IpUtils::checkIp($request->getClientIp(), $allowedIPs)) {
            throw new AccessDeniedHttpException('Nedostatečná oprávnění.');
        }
    }

    /**
     * @return Response
     */
    public function registerRoot(): Response
    {
        try {
            $this->appUserDefaultsService->registerRoot();

            return new Response('Uživatel byl vytvořen, pokud ještě neexistoval.');
        } catch (Exception $e) {
            return new Response('Nastala chyba při vytváření výchozího uživatele.');
        }
    }
}
