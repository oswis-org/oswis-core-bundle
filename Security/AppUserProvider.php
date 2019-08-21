<?php

namespace Zakjakub\OswisCoreBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Exceptions\OswisUserNotUniqueException;
use Zakjakub\OswisCoreBundle\Repository\AppUserRepository;

class AppUserProvider implements UserProviderInterface
{
    /**
     * @var AppUserRepository
     */
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->userRepository = $entityManager->getRepository(AppUserRepository::class);
    }

    /**
     * @param UserInterface $user
     *
     * @return AppUser|null
     * @throws OswisUserNotUniqueException
     * @throws UnsupportedUserException
     * @throws UsernameNotFoundException
     */
    final public function refreshUser(UserInterface $user): ?AppUser
    {
        if (!$user instanceof AppUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $username
     *
     * @return AppUser|null
     * @throws UsernameNotFoundException
     * @throws OswisUserNotUniqueException
     */
    final public function loadUserByUsername(
        /** @noinspection MissingParameterTypeDeclarationInspection */ $username
    ): ?AppUser {
        $user = $this->userRepository->loadUserByUsername($username);
        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
        assert($user instanceof AppUser);

        return $user;
    }

    final public function supportsClass(
        /** @noinspection MissingParameterTypeDeclarationInspection */ $class
    ): bool {
        return AppUser::class === $class;
    }
}
