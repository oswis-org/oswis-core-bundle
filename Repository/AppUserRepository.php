<?php

namespace Zakjakub\OswisResourcesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Zakjakub\OswisResourcesBundle\Entity\AppUser;

/**
 * AppUserRepository
 */
class AppUserRepository extends EntityRepository implements UserLoaderInterface
{

    /**
     * @param string $username
     *
     * @return AppUser|null
     */
    final public function loadUserByUsername(
        /** @noinspection MissingParameterTypeDeclarationInspection */
        $username
    ): ?AppUser {
        try {
            $appUser = $this->createQueryBuilder('u')
                ->where('(u.username = :username OR u.email = :email) AND (u.deleted IS NULL OR u.deleted = false)')
                ->setParameter('username', $username)
                ->setParameter('email', $username)
                ->getQuery()
                ->getOneOrNullResult();
            if (!$appUser) {
                return null;
            }
            \assert($appUser instanceof AppUser);

            return $appUser->isActive() ? $appUser : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
