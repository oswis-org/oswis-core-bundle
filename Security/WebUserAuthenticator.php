<?php
/**
 * @noinspection MethodVisibilityInspection
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Security;

use BadMethodCallException;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class WebUserAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'web_login';

    private UrlGeneratorInterface $urlGenerator;

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UrlGeneratorInterface $urlGenerator, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->urlGenerator = $urlGenerator;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    public function getCredentials(Request $request): array
    {
        return [
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?object
    {
        $user = $userProvider->loadUserByUsername($credentials['username']);
        if (!($user instanceof UserInterface)) {
            throw new CustomUserMessageAuthenticationException('Username could not be found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
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
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return Response
     * @throws BadMethodCallException
     * @throws InvalidArgumentException
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return null;
    }

    /**
     * @return string
     * @throws InvalidParameterException
     * @throws MissingMandatoryParametersException
     * @throws RouteNotFoundException
     */
    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
