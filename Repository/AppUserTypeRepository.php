<?php
/**
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Exception;
use Zakjakub\OswisCoreBundle\Entity\AppUserType;

class AppUserTypeRepository extends EntityRepository
{
    final public function findBySlug(string $slug): ?AppUserType
    {
        try {
            return $this->createQueryBuilder('t')->where('t.slug = :slug')->setParameter('slug', $slug)->getQuery()->getOneOrNullResult(Query::HYDRATE_OBJECT);
        } catch (Exception $e) {
            return null;
        }
    }

    final public function findOneBy(array $criteria, array $orderBy = null): ?AppUserType
    {
        $result = parent::findOneBy($criteria, $orderBy);

        return $result instanceof AppUserType ? $result : null;
    }

}
