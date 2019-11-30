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
use function assert;
use function get_class;

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
     * @throws OswisUserNotUniqueException
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
     * @noinspection MissingParameterTypeDeclarationInspection
     *
     * @throws OswisUserNotUniqueException
     * @throws UsernameNotFoundException
     */
    final public function loadUserByUsername($username): ?AppUser
    {
        $user = $this->userRepository->loadUserByUsername($username);
        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
        assert($user instanceof AppUser);

        return $user;
    }

    /**
     * @param string $class
     *
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    final public function supportsClass($class): bool
    {
        return AppUser::class === $class;
    }
}
