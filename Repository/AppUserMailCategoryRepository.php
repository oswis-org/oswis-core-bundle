<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use LogicException;
use OswisOrg\OswisCoreBundle\Entity\AppUserMail\AppUserMailCategory;

class AppUserMailCategoryRepository extends ServiceEntityRepository
{
    /**
     * @param  ManagerRegistry  $registry
     *
     * @throws LogicException
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppUserMailCategory::class);
    }

    final public function findByType(string $type): ?AppUserMailCategory
    {
        $queryBuilder = $this->createQueryBuilder('category');
        $queryBuilder->where("category.type = :type")->setParameter("type", $type);
        $queryBuilder->orderBy("category.priority", "DESC");
        $queryBuilder->setMaxResults(1);
        try {
            return $queryBuilder->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
        } catch (Exception $e) {
            return null;
        }
    }

    final public function findOneBy(array $criteria, array $orderBy = null): ?AppUserMailCategory
    {
        $result = parent::findOneBy($criteria, $orderBy);

        return $result instanceof AppUserMailCategory ? $result : null;
    }
}
