<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Repository\Web;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OswisOrg\OswisCoreBundle\Entity\Web\WebRedirect;

/**
 * @extends ServiceEntityRepository<WebRedirect>
 */
class WebRedirectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebRedirect::class);
    }

    /** Active (not soft-deleted) redirect for the public route; null renders 404. */
    public function findOneActiveBySlug(string $slug): ?WebRedirect
    {
        $result = $this->createQueryBuilder('r')
            ->where('r.slug = :slug')
            ->andWhere('r.deletedAt IS NULL')
            ->setParameter('slug', $slug)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result instanceof WebRedirect ? $result : null;
    }
}
