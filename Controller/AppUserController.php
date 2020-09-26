<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Controller;

use InvalidArgumentException;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserToken;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Exceptions\NotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Exceptions\TokenInvalidException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotUniqueException;
use OswisOrg\OswisCoreBundle\Form\Activation\ActivationRequestType;
use OswisOrg\OswisCoreBundle\Form\PasswordChange\PasswordChangeRequestType;
use OswisOrg\OswisCoreBundle\Form\PasswordChange\PasswordChangeType;
use OswisOrg\OswisCoreBundle\Provider\OswisCoreSettingsProvider;
use OswisOrg\OswisCoreBundle\Service\AppUserDefaultsService;
use OswisOrg\OswisCoreBundle\Service\AppUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Exception\OutOfBoundsException;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AppUserController extends AbstractController
{
    private AppUserService $appUserService;

    private AppUserDefaultsService $appUserDefaultsService;

    protected OswisCoreSettingsProvider $coreSettings;

    public function __construct(AppUserService $appUserService, AppUserDefaultsService $appUserDefaultsService, OswisCoreSettingsProvider $coreSettings)
    {
        $this->appUserService = $appUserService;
        $this->appUserDefaultsService = $appUserDefaultsService;
        $this->coreSettings = $coreSettings;
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws InvalidTypeException
     * @throws LogicException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     * @throws OutOfBoundsException
     * @throws RuntimeException
     * @throws UserNotFoundException
     * @throws UserNotUniqueException
     */
    public function passwordChangeRequest(Request $request): Response
    {
        $form = $this->createForm(PasswordChangeRequestType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $appUser = $this->appUserService->getRepository()->loadUserByUsername($form->get('username')->getData());
            if (null === $appUser) {
                throw new UserNotFoundException();
            }
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

    /**
     * @param Request $request
     *
     * @return Response
     * @throws InvalidTypeException
     * @throws LogicException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     * @throws OutOfBoundsException
     * @throws RuntimeException
     * @throws UserNotFoundException
     * @throws UserNotUniqueException
     */
    public function activationRequest(Request $request): Response
    {
        $form = $this->createForm(ActivationRequestType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $appUser = $this->appUserService->getRepository()->loadUserByUsername($form->get('username')->getData());
            if (null === $appUser) {
                throw new UserNotFoundException();
            }
            $this->appUserService->requestActivation($appUser);

            return $this->userActivationRequested();
        }

        return $this->render(
            '@OswisOrgOswisCore/web/pages/form.html.twig',
            [
                'form'  => $form->createView(),
                'title' => 'Aktivace účtu',
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

    /**
     * @param Request     $request
     * @param string|null $token
     * @param int|null    $appUserId
     *
     * @return Response
     * @throws InvalidTypeException
     * @throws LogicException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     * @throws OutOfBoundsException
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

    /**
     * @param AppUserToken $appUserToken
     *
     * @return Response
     * @throws InvalidTypeException
     * @throws NotFoundException
     */
    public function processTokenActivation(AppUserToken $appUserToken): Response
    {
        $this->appUserService->activate($appUserToken->getAppUser(), true);

        return $this->userActivated();
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

    /**
     * @param AppUserToken $appUserToken
     * @param Request      $request
     *
     * @return Response
     * @throws InvalidTypeException
     * @throws LogicException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     * @throws OutOfBoundsException
     * @throws RuntimeException
     * @throws TokenInvalidException
     */
    public function processTokenPasswordChange(AppUserToken $appUserToken, Request $request): Response
    {
        $form = $this->createForm(PasswordChangeType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $appUserToken->use();
            $this->appUserService->changePassword($appUserToken->getAppUser(), $form->get('password')->getData(), true);

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

    /**
     * @param Request $request
     *
     * @return Response
     * @throws AccessDeniedHttpException
     */
    public function registerRoot(Request $request): Response
    {
        $allowedIPs = $this->coreSettings->getAdminIPs();
        if (!IpUtils::checkIp($request->getClientIp(), $allowedIPs)) {
            throw new AccessDeniedHttpException('Nedostatečná oprávnění.');
        }
        try {
            $this->appUserDefaultsService->registerRoot();

            return new Response('Uživatel byl vytvořen, pokud ještě neexistoval.');
        } catch (InvalidTypeException|InvalidArgumentException|UserNotFoundException|NotFoundException|NotImplementedException|UserNotUniqueException|OswisException $e) {
            return new Response('Nastala chyba při vytváření výchozího uživatele.');
        }
    }
}
