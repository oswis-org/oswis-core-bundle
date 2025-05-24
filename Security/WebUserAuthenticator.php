<?php

/**
 * @noinspection MethodVisibilityInspection
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Security;

use InvalidArgumentException;
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
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class WebUserAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'oswis_org_oswis_core_web_admin_login';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordEncoder,
        private readonly RouterInterface $router,
    )
    {
    }

    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    /**
     * @param array{username?: string, identifier?: string, password?: string} $credentials
     *
     * @throws UserNotFoundException
     */
    public function getUser(mixed $credentials, UserProviderInterface $userProvider): ?object
    {
        return $userProvider->loadUserByIdentifier($credentials['username'] ?? '');
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     *
     * @param array{identifier?: string, password?: string} $credentials
     *
     * @return string|null
     */
    public function getPassword(array $credentials): ?string
    {
        return $credentials['password'] ?? '';
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $firewallName
     *
     * @return Response|null
     * @throws InvalidArgumentException|SessionNotFoundException
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return null;
    }

    /**
     * @throws InvalidArgumentException
     * @throws BadRequestException|BadCredentialsException
     */
    public function authenticate(Request $request): Passport
    {
        /** @var array{identifier?: string, username?: string, password?: string, remember_me?: bool} $userCredentials */
        $userCredentials = $this->getCredentials($request);

        return new Passport(
            new UserBadge($userCredentials['username'] ?? ''),
            new CustomCredentials(
            /** @var array{identifier?: string, username?: string, password?: string, remember_me?: bool} $credentials */
                function (mixed $credentials, UserInterface $user) {
                    assert(is_array($credentials));

                    /** @phpstan-ignore-next-line */
                    return $this->checkCredentials($credentials, $user);
                },
                $userCredentials,
            ),
            ($userCredentials['remember_me'] ?? false) ? [new RememberMeBadge()] : [],
        );
    }

    /**
     * @return array{username?: string, password?: string, remember_me?: bool}
     *
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function getCredentials(Request $request): array
    {
        $username = $request->request->get('_username');
        assert(is_string($username));
        $password = $request->request->get('_password');
        assert(is_string($password));
        $rememberMe = $request->request->get('_remember_me');
        assert(is_bool($rememberMe));

        return ['username' => $username, 'password' => $password, 'remember_me' => $rememberMe];
    }

    /**
     * @param array{username?: string, password?: string, _remember_me?: bool} $credentials
     * @param UserInterface                                                    $user
     * @return bool
     */
    public function checkCredentials(mixed $credentials, UserInterface $user): bool
    {
        assert($user instanceof PasswordAuthenticatedUserInterface);

        return $this->passwordEncoder->isPasswordValid($user, $credentials['password'] ?? '');
    }

    /**
     * @throws RouteNotFoundException
     * @throws MissingMandatoryParametersException
     * @throws SessionNotFoundException
     * @throws InvalidArgumentException
     * @throws InvalidParameterException
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($this->router->generate(self::LOGIN_ROUTE));
    }
}
