<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Security;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class WebUserAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'oswis_org_oswis_core_web_admin_login';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordEncoder,
        private readonly RouterInterface $router,
    ) {
    }

    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    /**
     * @throws BadRequestException|BadCredentialsException
     */
    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);
        $passwordEncoder = $this->passwordEncoder;

        return new Passport(
            new UserBadge($credentials['username']),
            new CustomCredentials(
                static function (mixed $given, UserInterface $user) use ($passwordEncoder): bool {
                    if (!is_array($given) || !is_string($given['password'] ?? null)) {
                        return false;
                    }
                    if (!$user instanceof PasswordAuthenticatedUserInterface) {
                        return false;
                    }

                    return $passwordEncoder->isPasswordValid($user, $given['password']);
                },
                $credentials,
            ),
            $credentials['remember_me'] ? [new RememberMeBadge()] : [],
        );
    }

    /**
     * @return array{username: string, password: string, remember_me: bool}
     *
     * @throws BadRequestException
     */
    private function getCredentials(Request $request): array
    {
        // Request::getString() / getBoolean() return concrete types with safe defaults,
        // so we don't need defensive assert()/instanceof on the result.
        return [
            'username'    => $request->request->getString('_username'),
            'password'    => $request->request->getString('_password'),
            'remember_me' => $request->request->getBoolean('_remember_me'),
        ];
    }

    /**
     * @throws SessionNotFoundException
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return null;
    }

    /**
     * @throws RouteNotFoundException
     * @throws MissingMandatoryParametersException
     * @throws SessionNotFoundException
     * @throws InvalidParameterException
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($this->router->generate(self::LOGIN_ROUTE));
    }
}
