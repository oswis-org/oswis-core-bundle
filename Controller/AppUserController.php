<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Controller;

use Exception;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotFoundException;
use OswisOrg\OswisCoreBundle\Service\AppUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AppUserController extends AbstractController
{
    private AppUserService $appUserService;

    public function __construct(AppUserService $appUserService)
    {
        $this->appUserService = $appUserService;
    }

    /**
     * @param string|null $token Secret token for user activation (sent by e-mail).
     *
     * @return Response
     */
    final public function appUserActivationAction(?string $token = null): Response
    {
        try {
            $this->appUserService->appUserAction(null, AppUserService::ACTIVATION, null, $token);
            $data = [
                'title'   => 'Účet aktivován!',
                'message' => 'Účet byl úspěšně aktivován.',
            ];

            return $this->render('@OswisOrgOswisCore/web/pages/message.html.twig', $data ?? []);
        } catch (OswisException|UserNotFoundException|NotImplementedException $e) {
            $data = [
                'title'   => 'Účet nebyl aktivován!',
                'message' => $e->getMessage(),
            ];

            return $this->render('@OswisOrgOswisCore/web/pages/message.html.twig', $data ?? []);
        }
    }

    public function registerRoot(): Response
    {
        try {
            $this->appUserService->registerRoot();

            return new Response('Uživatel byl vytvořen, pokud ještě neexistoval.');
        } catch (Exception $e) {
            return new Response('Nastala chyba při vytváření uživatele.');
        }
    }
}
