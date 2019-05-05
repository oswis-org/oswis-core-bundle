<?php

namespace Zakjakub\OswisCoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use function assert;

/**
 * AppUserRepository
 */
class AppUserRepository extends EntityRepository implements UserLoaderInterface
{

    /**
     * @param string $username
     *
     * @return AppUser|null
     * @throws NonUniqueResultException
     * @throws Exception
     */
    final public function loadUserByUsername(
        /** @noinspection MissingParameterTypeDeclarationInspection */
        $username
    ): ?AppUser {
        $appUser = $this->createQueryBuilder('u')
            ->where('(u.username = :username OR u.email = :email) AND (u.deleted IS NULL OR u.deleted = false)')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
        if (!$appUser) {
            return null;
        }
        assert($appUser instanceof AppUser);

        return $appUser->isActive() ? $appUser : null;
    }
}
