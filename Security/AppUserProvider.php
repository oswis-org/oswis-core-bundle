<?php

namespace OswisOrg\OswisCoreBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Exceptions\OswisUserNotUniqueException;
use OswisOrg\OswisCoreBundle\Repository\AppUserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use function get_class;

class AppUserProvider implements UserProviderInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
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
     * @throws OswisUserNotUniqueException
     * @throws UsernameNotFoundException
     */
    final public function loadUserByUsername($username): ?AppUser
    {
        $userRepository = $this->em->getRepository(AppUserRepository::class);
        assert($userRepository instanceof AppUserRepository);
        $user = $userRepository->loadUserByUsername($username);
        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

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
