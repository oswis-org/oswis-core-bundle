<?php
/**
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Exception;
use Zakjakub\OswisCoreBundle\Entity\AppUserRole;

class AppUserRoleRepository extends EntityRepository
{
    final public function findBySlug(string $slug): ?AppUserRole
    {
        try {
            return $this->createQueryBuilder('r')->where('r.slug = :slug')->setParameter('slug', $slug)->getQuery()->getOneOrNullResult(Query::HYDRATE_OBJECT);
        } catch (Exception $e) {
            return null;
        }
    }

    final public function findOneBy(array $criteria, array $orderBy = null): ?AppUserRole
    {
        $result = parent::findOneBy($criteria, $orderBy);

        return $result instanceof AppUserRole ? $result : null;
    }

}
