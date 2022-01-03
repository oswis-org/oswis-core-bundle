<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use LogicException;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserRole;

class AppUserRoleRepository extends ServiceEntityRepository
{
    /**
     * @param  ManagerRegistry  $registry
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
            return ($result = $query->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT)) instanceof AppUserRole ? $result : null;
        } catch (Exception) {
            return null;
        }
    }

    final public function findOneBy(array $criteria, array $orderBy = null): ?AppUserRole
    {
        $result = parent::findOneBy($criteria, $orderBy);

        return $result instanceof AppUserRole ? $result : null;
    }
}
