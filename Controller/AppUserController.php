<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller;

use InvalidArgumentException;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserToken;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractToken;
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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Exception\OutOfBoundsException;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AppUserController extends AbstractController
{
    public const TEMPLATE_FORM    = '@OswisOrgOswisCore/web/pages/form.html.twig';
    public const TEMPLATE_MESSAGE = '@OswisOrgOswisCore/web/pages/message.html.twig';

    public function __construct(
        private readonly AppUserService $appUserService,
        protected AppUserDefaultsService $appUserDefaultsService,
        protected OswisCoreSettingsProvider $coreSettings
    ) {
    }

    /**
     * @param  Request  $request
     *
     * @return Response
     * @throws InvalidTypeException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     * @throws TokenInvalidException
     * @throws UserNotFoundException
     * @throws UserNotUniqueException
     * @throws LogicException
     * @throws OutOfBoundsException
     * @throws RuntimeException
     */
    public function passwordChangeRequest(Request $request): Response
    {
        $form = $this->createForm(PasswordChangeRequestType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && is_string($username = $form->get('username')->getData())) {
            $appUser = $this->appUserService->getRepository()->loadUserByIdentifier($username);
            if (!$appUser instanceof AppUser) {
                throw new UserNotFoundException();
            }
            $this->appUserService->requestPasswordChange($appUser, true);

            return $this->passwordChangeRequested();
        }

        return $this->render(self::TEMPLATE_FORM, [
            'form'  => $form->createView(),
            'title' => 'Změna hesla',
        ]);
    }

    public function passwordChangeRequested(): Response
    {
        return $this->render(self::TEMPLATE_MESSAGE, [
            'title'   => 'Žádost o změnu hesla odeslána!',
            'message' => 'Žádost o změnu hesla u uživatelského účtu byla úspěšně zpracována a na e-mail byl odeslán odkaz pro jeho změnu.',
        ]);
    }

    /**
     * @param  Request  $request
     *
     * @return Response
     * @throws InvalidTypeException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     * @throws TokenInvalidException
     * @throws UserNotFoundException
     * @throws UserNotUniqueException
     * @throws LogicException
     * @throws OutOfBoundsException
     * @throws RuntimeException
     */
    public function activationRequest(Request $request): Response
    {
        $form = $this->createForm(ActivationRequestType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && is_string($username = $form->get('username')->getData())) {
            $appUser = $this->appUserService->getRepository()->loadUserByIdentifier($username);
            if (!$appUser instanceof AppUser) {
                throw new UserNotFoundException();
            }
            $this->appUserService->requestActivation($appUser);

            return $this->userActivationRequested();
        }

        return $this->render(self::TEMPLATE_FORM, [
            'form'  => $form->createView(),
            'title' => 'Aktivace účtu',
        ]);
    }

    public function userActivationRequested(): Response
    {
        return $this->render(self::TEMPLATE_MESSAGE, [
            'title'   => 'Žádost o aktivaci účtu odeslána!',
            'message' => 'Žádost o aktivaci uživatelského účtu byla úspěšně zpracována a na e-mail byl odeslán odkaz pro její provedení.',
        ]);
    }

    /**
     * @param  Request  $request
     * @param  string|null  $token
     * @param  int|null  $appUserId
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
     * @param  AppUserToken  $appUserToken
     *
     * @return Response
     * @throws InvalidTypeException
     * @throws NotFoundException
     * @throws TokenInvalidException
     */
    public function processTokenActivation(AppUserToken $appUserToken): Response
    {
        $this->appUserService->activate($appUserToken->getAppUser());

        return $this->userActivated();
    }

    public function userActivated(): Response
    {
        return $this->render(self::TEMPLATE_MESSAGE, [
            'title'   => 'Účet aktivován!',
            'message' => 'Uživatelský účet byl úspěšně aktivován.',
        ]);
    }

    /**
     * @param  AppUserToken  $appUserToken
     * @param  Request  $request
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
            $password = $form->get('password')->getData();
            if (empty($password) || !is_string($password)) {
                $password = null;
            }
            $this->appUserService->changePassword($appUserToken->getAppUser(), $password, true);

            return $this->passwordChanged();
        }

        return $this->render(self::TEMPLATE_FORM, [
            'form'  => $form->createView(),
            'title' => 'Změna hesla',
        ]);
    }

    public function passwordChanged(): Response
    {
        return $this->render(self::TEMPLATE_MESSAGE, [
            'title'   => 'Heslo změněno!',
            'message' => 'Heslo u uživatelského účtu bylo úspěšně změněno.',
        ]);
    }

    /**
     * Consume a TYPE_REGISTRATION_LOGIN token: log the bound AppUser into
     * the `main` session firewall and bounce the browser onto the
     * registration form for the offer encoded in `rangeSlug`. Single-use,
     * 24-hour TTL per the token entity defaults.
     *
     * @throws InvalidTypeException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     * @throws TokenInvalidException
     */
    public function registrationLogin(
        Security $security,
        EntityManagerInterface $entityManager,
        string $token,
        int $appUserId,
        string $rangeSlug,
    ): Response {
        try {
            $appUserToken = $this->appUserService->getVerifiedToken($token, $appUserId);
            if (AbstractToken::TYPE_REGISTRATION_LOGIN !== $appUserToken->getType()) {
                throw new TokenInvalidException('Tento odkaz neslouží k přihlášení k registraci.');
            }
            $appUserToken->use();
            $appUser = $appUserToken->getAppUser();
            if (!$appUser instanceof AppUser) {
                throw new UserNotFoundException();
            }
            $entityManager->flush();
        } catch (TokenInvalidException|UserNotFoundException) {
            // Don't let TokenInvalidException (HTTP 403) bubble — Symfony's
            // access listener would redirect to /web_admin/login, which is
            // wrong context for a regular visitor whose magic-link expired.
            return $this->render(self::TEMPLATE_MESSAGE, [
                'title'   => 'Odkaz již není platný',
                'message' => 'Tento odkaz pro pokračování v přihlášce už vypršel, byl použit, '
                             .'nebo není platný. Zkus prosím odeslat přihlašovací formulář znovu '
                             .'— pošleme Ti nový odkaz.',
            ]);
        }
        // Symfony 7/8: explicit authenticator name is required when the
        // firewall has >1 authenticator. "main" firewall has both
        // WebUserAuthenticator (custom) and form_login. Magic-link login
        // skips the password challenge — use the form_login flow so the
        // session + remember-me cookie are set the same way as a normal
        // /web_admin/login_check would set them.
        $security->login($appUser, 'form_login', 'main');

        return $this->redirectToRoute('oswis_org_oswis_calendar_web_registration', [
            'rangeSlug' => $rangeSlug,
        ]);
    }

    /**
     * @param  Request  $request
     *
     * @return Response
     * @throws AccessDeniedHttpException
     */
    public function registerRoot(Request $request): Response
    {
        $allowedIPs = $this->coreSettings->getAdminIPs();
        if (!IpUtils::checkIp(''.$request->getClientIp(), $allowedIPs)) {
            throw new AccessDeniedHttpException('Nedostatečná oprávnění.');
        }
        try {
            $this->appUserDefaultsService->registerRoot();

            return new Response('Uživatel byl vytvořen, pokud ještě neexistoval.');
        } catch (InvalidTypeException|InvalidArgumentException|UserNotFoundException|NotFoundException|NotImplementedException|UserNotUniqueException|OswisException) {
            return new Response('Nastala chyba při vytváření výchozího uživatele.');
        }
    }
}
