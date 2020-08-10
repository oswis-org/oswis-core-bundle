<?php

namespace OswisOrg\OswisCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use LogicException;
use OswisOrg\OswisCoreBundle\Entity\TwigTemplate\TwigTemplate;

class TwigTemplateRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     *
     * @throws LogicException
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TwigTemplate::class);
    }

    final public function findBySlug(string $slug): ?TwigTemplate
    {
        $queryBuilder = $this->createQueryBuilder('template');
        $queryBuilder->setParameter("slug", $slug);
        $queryBuilder->where("template.slug = :slug");
        $queryBuilder->orderBy("template.id", "ASC");
        $queryBuilder->setMaxResults(1);
        try {
            return $queryBuilder->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT) ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    final public function findOneBy(array $criteria, array $orderBy = null): ?TwigTemplate
    {
        $result = parent::findOneBy($criteria, $orderBy);

        return $result instanceof TwigTemplate ? $result : null;
    }
}
