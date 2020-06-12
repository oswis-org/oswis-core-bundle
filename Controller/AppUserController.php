<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Controller;

use Exception;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserToken;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Exceptions\TokenInvalidException;
use OswisOrg\OswisCoreBundle\Form\AbstractClass\PasswordChangeRequestType;
use OswisOrg\OswisCoreBundle\Form\AbstractClass\PasswordChangeType;
use OswisOrg\OswisCoreBundle\Service\AppUserDefaultsService;
use OswisOrg\OswisCoreBundle\Service\AppUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppUserController extends AbstractController
{
    private AppUserService $appUserService;

    private AppUserDefaultsService $appUserDefaultsService;

    public function __construct(AppUserService $appUserService, AppUserDefaultsService $appUserDefaultsService)
    {
        $this->appUserService = $appUserService;
        $this->appUserDefaultsService = $appUserDefaultsService;
    }

    public function passwordChangeRequest(Request $request, ?int $appUserId): Response
    {
        $appUser = $this->appUserService->getRepository()->loadUserById($appUserId);
        $form = $this->createForm(PasswordChangeRequestType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->appUserService->requestPasswordChange($appUser, true);

            return $this->passwordChangeRequested();
        }

        return $this->render(
            '@OswisOrgOswisCore/web/pages/form.html.twig',
            [
                'form'  => $form->createView(),
                'title' => 'Změna hesla',
            ]
        );
    }

    /**
     * @param string|null  $token
     * @param int|null     $appUserId
     * @param Request|null $request
     *
     * @return Response
     * @throws LogicException
     * @throws OswisException
     * @throws RuntimeException
     * @throws TokenInvalidException
     */
    public function processToken(Request $request, ?string $token = null, ?int $appUserId = null): Response
    {
        $appUserToken = $this->appUserService->getVerifiedToken($token, $appUserId);
        $type = $appUserToken->getType();
        if (AppUserService::ACTIVATION === $type) {
            return $this->processTokenActivation($appUserToken);
        }
        if (AppUserService::PASSWORD_CHANGE === $type) {
            return $this->processTokenPasswordChange($appUserToken, $request);
        }
        throw new TokenInvalidException('nebyla vykonána žádná akce');
    }

    public function userActivated(): Response
    {
        return $this->render(
            '@OswisOrgOswisCore/web/pages/message.html.twig',
            [
                'title'   => 'Účet aktivován!',
                'message' => 'Uživatelský účet byl úspěšně aktivován.',
            ]
        );
    }

    public function passwordChanged(): Response
    {
        return $this->render(
            '@OswisOrgOswisCore/web/pages/message.html.twig',
            [
                'title'   => 'Heslo změněno!',
                'message' => 'Heslo u uživatelského účtu bylo úspěšně změněno.',
            ]
        );
    }

    public function passwordChangeRequested(): Response
    {
        return $this->render(
            '@OswisOrgOswisCore/web/pages/message.html.twig',
            [
                'title'   => 'Žádost o změnu hesla odeslána!',
                'message' => 'Žádost o změnu hesla u uživatelského účtu byla úspěšně zpracována a na e-mail byl odeslán odkaz pro jeho změnu.',
            ]
        );
    }

    public function userActivationRequested(): Response
    {
        return $this->render(
            '@OswisOrgOswisCore/web/pages/message.html.twig',
            [
                'title'   => 'Žádost o aktivaci účtu odeslána!',
                'message' => 'Žádost o aktivaci uživatelského účtu byla úspěšně zpracována a na e-mail byl odeslán odkaz pro její provedení.',
            ]
        );
    }

    public function registerRoot(): Response
    {
        try {
            $this->appUserDefaultsService->registerRoot();

            return new Response('Uživatel byl vytvořen, pokud ještě neexistoval.');
        } catch (Exception $e) {
            return new Response('Nastala chyba při vytváření výchozího uživatele.');
        }
    }

    /**
     * @param AppUserToken $appUserToken
     * @param Request      $request
     *
     * @return Response
     * @throws OswisException
     * @throws TokenInvalidException
     * @throws LogicException
     * @throws RuntimeException
     */
    public function processTokenPasswordChange(AppUserToken $appUserToken, Request $request): Response
    {
        $form = $this->createForm(PasswordChangeType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $appUserToken->use();
            $this->appUserService->changePassword($appUserToken->getAppUser(), $form->getData()['password'], true);

            return $this->passwordChanged();
        }

        return $this->render(
            '@OswisOrgOswisCore/web/pages/form.html.twig',
            [
                'form'  => $form->createView(),
                'title' => 'Změna hesla',
            ]
        );
    }

    public function processTokenActivation(AppUserToken $appUserToken): Response
    {
        $this->appUserService->activate($appUserToken->getAppUser(), null, true);

        return $this->userActivated();
    }
}
