<?php

declare(strict_types=1);

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
     * @param  ManagerRegistry  $registry
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
            return ($result = $queryBuilder->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT))
                   instanceof
                   TwigTemplate ? $result : null;
        } catch (Exception) {
            return null;
        }
    }

    /**
     * Řádek šablony pro Twig loader čtený přímo z DB — ne z identity mapy ani z L2 cache.
     *
     * PROČ: loader z něj skládá klíč zkompilované šablony i její zdroj; oba musí pocházet ze stejného
     * stavu databáze, i v dlouho běžícím procesu (cron), kde by entita v identity mapě zůstala stará.
     *
     * @return array{id: int, textValue: ?string, regularTemplateName: ?string}|null
     */
    public function findLoaderRowBySlug(string $slug): ?array
    {
        $row = $this->getEntityManager()->getConnection()->fetchAssociative(
            'SELECT id, text_value, regular_template_name FROM core_twig_template WHERE slug = ? ORDER BY id ASC LIMIT 1',
            [$slug],
        );
        if (false === $row) {
            return null;
        }
        $id = $row['id'] ?? null;
        $text = $row['text_value'] ?? null;
        $regular = $row['regular_template_name'] ?? null;

        return [
            'id'                  => is_numeric($id) ? (int) $id : 0,
            'textValue'           => is_string($text) ? $text : null,
            'regularTemplateName' => is_string($regular) && '' !== $regular ? $regular : null,
        ];
    }

    final public function findOneBy(array $criteria, ?array $orderBy = null): ?TwigTemplate
    {
        $result = parent::findOneBy($criteria, $orderBy);

        return $result instanceof TwigTemplate ? $result : null;
    }
}
