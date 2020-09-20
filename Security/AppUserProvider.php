<?php

namespace OswisOrg\OswisCoreBundle\Security;

use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotUniqueException;
use OswisOrg\OswisCoreBundle\Repository\AppUserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use function get_class;

class AppUserProvider implements UserProviderInterface
{
    private AppUserRepository $appUserRepository;

    public function __construct(AppUserRepository $appUserRepository)
    {
        $this->appUserRepository = $appUserRepository;
    }

    /**
     * @throws UserNotUniqueException
     * @throws UnsupportedUserException
     * @throws UsernameNotFoundException
     */
    final public function refreshUser(UserInterface $user): ?AppUser
    {
        if (!$user instanceof AppUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $username
     *
     * @return AppUser|null
     * @throws UserNotUniqueException
     * @throws UsernameNotFoundException
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    final public function loadUserByUsername(string $username): ?AppUser
    {
        if (null === ($user = $this->appUserRepository->loadUserByUsername($username))) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    /**
     * @param string $class
     *
     * @return bool
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    final public function supportsClass(string $class): bool
    {
        return AppUser::class === $class;
    }
}
