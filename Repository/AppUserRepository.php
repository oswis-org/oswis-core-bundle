<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use OswisOrg\OswisCoreBundle\Entity\AppUser;
use OswisOrg\OswisCoreBundle\Exceptions\OswisUserNotUniqueException;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class AppUserRepository extends EntityRepository implements UserLoaderInterface
{
    /**
     * @param string|null $username
     *
     * @throws OswisUserNotUniqueException
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    public function loadUserByUsername($username): ?AppUser
    {
        if (!$username) {
            return null;
        }
        try {
            $builder = $this->createQueryBuilder('u')
                ->where('(u.username = :username OR u.email = :email) AND (u.deleted IS NULL OR u.deleted = false)');
            $query = $builder->setParameter('username', $username)
                ->setParameter('email', $username)
                ->getQuery();
            $appUser = $query->getOneOrNullResult(Query::HYDRATE_OBJECT);
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
    public function loadUserById(?int $id): ?AppUser
    {
        if (!$id) {
            return null;
        }
        try {
            $builder = $this->createQueryBuilder('u')
                ->where('(u.id = :id) AND (u.deleted IS NULL OR u.deleted = false)');
            $appUser = $builder->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult(Query::HYDRATE_OBJECT);
        } catch (NonUniqueResultException $e) {
            throw new OswisUserNotUniqueException();
        }

        return ($appUser && ($appUser instanceof AppUser) && $appUser->isActive()) ? $appUser : null;
    }

    public function findByEmail(string $email): Collection
    {
        $builder = $this->createQueryBuilder('app_user')
            ->where('app_user.email = :email')
            ->setParameter('email', $email);

        return new ArrayCollection(
            $builder->getQuery()
                ->getResult(Query::HYDRATE_OBJECT)
        );
    }

    final public function findOneBy(array $criteria, array $orderBy = null): ?AppUser
    {
        $result = parent::findOneBy($criteria, $orderBy);

        return $result instanceof AppUser ? $result : null;
    }
}
