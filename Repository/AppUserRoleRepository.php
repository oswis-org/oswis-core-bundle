<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Exception;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserRole;

class AppUserRoleRepository extends EntityRepository
{
    final public function findBySlug(string $slug): ?AppUserRole
    {
        $query = $this->createQueryBuilder('r')->where('r.slug = :slug')->setParameter('slug', $slug)->getQuery();
        try {
            return $query->getOneOrNullResult(Query::HYDRATE_OBJECT);
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
