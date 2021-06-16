<?php

namespace OswisOrg\OswisCoreBundle\Security;

use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotUniqueException;
use OswisOrg\OswisCoreBundle\Repository\AppUserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
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
     * @throws UserNotFoundException
     */
    final public function refreshUser(UserInterface $user): ?AppUser
    {
        if (!$user instanceof AppUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername(''.$user->getUsername());
    }

    /**
     * @param  string  $username
     *
     * @return AppUser|null
     * @throws UserNotUniqueException
     * @throws UserNotFoundException
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    final public function loadUserByUsername(string $username): ?AppUser
    {
        if (null === ($user = $this->appUserRepository->loadUserByUsername($username))) {
            throw new UserNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    /**
     * @param  string  $class
     *
     * @return bool
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    final public function supportsClass(string $class): bool
    {
        return AppUser::class === $class;
    }

    /**
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\UserNotUniqueException
     * @throws \Symfony\Component\Security\Core\Exception\UserNotFoundException
     */
    final public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->loadUserByUsername($identifier) ?? throw new UserNotFoundException();
    }
}
