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
     * @throws UserNotFoundException
     */
    public function getUser(mixed $credentials, UserProviderInterface $userProvider): ?object
    {
        return $userProvider->loadUserByIdentifier(is_array($credentials) ? $credentials['username'] : null);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     *
     * @param array $credentials
     *
     * @return string|null
     */
    public function getPassword(array $credentials): ?string
    {
        return $credentials['password'];
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
        $userCredentials = $this->getCredentials($request);

        return new Passport(
            new UserBadge($userCredentials['username']),
            new CustomCredentials(
                function (mixed $credentials, UserInterface $user) {
                    return $this->checkCredentials($credentials, $user);
                },
                $userCredentials,
            ),
            [
                new RememberMeBadge(),
            ],
        );
    }

    /**
     * @throws InvalidArgumentException
     * @throws BadRequestException
     */
    public function getCredentials(Request $request): array
    {
        return [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
        ];
    }

    public function checkCredentials(mixed $credentials, UserInterface $user): bool
    {
        assert($user instanceof PasswordAuthenticatedUserInterface);
        assert(is_array($credentials));

        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
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
