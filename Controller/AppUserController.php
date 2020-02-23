<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Controller;

use Exception;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Zakjakub\OswisCoreBundle\Exceptions\OswisException;
use Zakjakub\OswisCoreBundle\Service\AppUserService;

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
     * @throws LogicException
     */
    final public function appUserActivationAction(?string $token = null): Response
    {
        try {
            $this->appUserService->appUserAction(null, 'activation', null, $token);
            $data = [
                'title'   => 'Účet aktivován!',
                'message' => 'Účet byl úspěšně aktivován.',
            ];

            return $this->render('@ZakjakubOswisCore/web/pages/message.html.twig', $data ?? []);
        } catch (OswisException $e) {
            $data = [
                'title'   => 'Účet nebyl aktivován!',
                'message' => $e->getMessage(),
            ];

            return $this->render('@ZakjakubOswisCore/web/pages/message.html.twig', $data ?? []);
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
