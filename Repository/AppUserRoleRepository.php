<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use LogicException;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserRole;

class AppUserRoleRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     *
     * @throws LogicException
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppUserRole::class);
    }

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
