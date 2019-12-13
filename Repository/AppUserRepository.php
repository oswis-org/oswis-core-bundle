<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Exceptions\OswisUserNotUniqueException;

/**
 * Repository of application users.
 * @todo Optimize queries!
 */
class AppUserRepository extends EntityRepository implements UserLoaderInterface
{
    /**
     * @param string|null $username
     *
     * @throws OswisUserNotUniqueException
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    final public function loadUserByUsername($username): ?AppUser
    {
        if (!$username) {
            return null;
        }
        try {
            $qb = $this->createQueryBuilder('u')->where('(u.username = :username OR u.email = :email) AND (u.deleted IS NULL OR u.deleted = false)');
            $appUser = $qb->setParameter('username', $username)->setParameter('email', $username)->getQuery()->getOneOrNullResult(Query::HYDRATE_OBJECT);
        } catch (NonUniqueResultException $e) {
            throw new OswisUserNotUniqueException();
        }

        return $appUser && ($appUser instanceof AppUser) && $appUser->isActive() ? $appUser : null;
    }

    /**
     * @param int $id
     *
     * @throws OswisUserNotUniqueException
     */
    final public function loadUserById(?int $id): ?AppUser
    {
        if (!$id) {
            return null;
        }
        try {
            $qb = $this->createQueryBuilder('u')->where('(u.id = :id) AND (u.deleted IS NULL OR u.deleted = false)');
            $appUser = $qb->setParameter('id', $id)->getQuery()->getOneOrNullResult(Query::HYDRATE_OBJECT);
        } catch (NonUniqueResultException $e) {
            throw new OswisUserNotUniqueException();
        }

        return $appUser && ($appUser instanceof AppUser) && $appUser->isActive() ? $appUser : null;
    }

    final public function findByEmail(string $email): Collection
    {
        return new ArrayCollection(
            $this->createQueryBuilder('app_user')->where('app_user.email = :email')->setParameter('email', $email)->getQuery()->getResult(Query::HYDRATE_OBJECT)
        );
    }
}
