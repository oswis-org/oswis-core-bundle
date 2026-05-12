<?php

declare(strict_types=1);

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
    final public function refreshUser(UserInterface $user): AppUser
    {
        if (!$user instanceof AppUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $loaded = $this->loadUserByIdentifier(''.$user->getUserIdentifier());
        assert($loaded instanceof AppUser);

        return $loaded;
    }

    final public function supportsClass(string $class): bool
    {
        return AppUser::class === $class;
    }

    /**
     * @throws UserNotUniqueException
     * @throws UserNotFoundException
     */
    final public function loadUserByIdentifier(string $identifier): UserInterface
    {
        if (null === ($user = $this->appUserRepository->loadUserByIdentifier($identifier))) {
            throw new UserNotFoundException(sprintf('Identifier "%s" does not exist.', $identifier));
        }

        return $user;
    }
}
