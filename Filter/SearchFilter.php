<?php
/**
 * @noinspection PhpComposerExtensionStubsInspection
 * @noinspection MissingParameterTypeDeclarationInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use ReflectionClass;
use ReflectionException;

/**
 * @noinspection ClassNameCollisionInspection
 */
final class SearchFilter extends AbstractFilter
{
    /** Parametr dotazu — zároveň značka, že hledání už v dotazu je. */
    private const string PARAMETR = 'oswis_search';

    /**
     * Popisná pole, ve kterých se hledá u entity bez `#[SearchAnnotation]` (jen ta, která entita má). Záměrně
     * NE `note`, `internalNote`, hesla, tokeny ani e-mail — hledání podle pole, které člen v API nevidí, by jeho
     * obsah prozradilo (opakovaným hledáním). Entita, která chce víc, to řekne atributem.
     */
    private const array VYCHOZI_POLE = ['id', 'name', 'shortName', 'sortableName', 'slug', 'description', 'givenName', 'additionalName', 'familyName', 'nickname', 'title'];

    /**
     * @throws ReflectionException
     */
    public function getDescription(string $resourceClass): array
    {
        $annotation = self::readSearchAttribute($resourceClass);

        return [
            'search' => [
                'property' => 'search',
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'FullTextFilter on '.implode(
                        ', ',
                        $annotation->fields ?? []
                    ),
                ],
            ],
        ];
    }

    /**
     * Fulltext `?search=` nad poli z `#[SearchAnnotation]`, jinak nad popisnými poli entity.
     *
     * Oprava 1. 10. 2026 (produkce 29. 9.: hledání osoby v mobilní aplikaci vracelo 400): filtr dostávají
     * VŠECHNY entity přes core traity (`NameTrait`, `DescriptionTrait`… mají `#[ApiFilter(SearchFilter::class)]`),
     * ale atribut s poli mělo jen pár z nich — ostatní vracely 400 „No Search implemented", a mobilní aplikace
     * posílá `search` u každého seznamu. Teď:
     *  - bez atributu se hledá v {@see self::VYCHOZI_POLE}, která entita opravdu má (nikdy interní poznámka ani tajnosti);
     *  - pole z atributu, které v entitě není, se přeskočí a zaloguje (dřív chyba DQL = 500);
     *  - na dotaz se hledání použije JEDNOU (entita mívá filtr dvakrát — z traitů i ze třídy; dvojí JOIN
     *    se stejným aliasem padal na 500 a dvojí podmínka by výsledky zúžila);
     *  - aliasy JOINů generuje API Platform, kořen se bere z dotazu (dřív natvrdo `o`).
     *
     * @param mixed $value
     */
    public function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if ('search' !== $property) {
            return;
        }
        $stringValue = trim(self::mixedToString($value));
        // Skip the unindexed multi-column LOWER(...) LIKE '%term%' scan (with auto
        // leftJoins) for too-short terms: a single character matches almost every
        // row across every searched field/join — the worst-case full scan.
        if (mb_strlen($stringValue) < 2) {
            return;
        }
        if (null !== $queryBuilder->getParameter(self::PARAMETR)) {
            return;
        }
        $fields = $this->poleKHledani($resourceClass, $queryBuilder);
        if ([] === $fields) {
            $this->logger->info(sprintf('Search: %s nemá pole k hledání — parametr search se ignoruje.', $resourceClass));

            return;
        }
        $rootAlias = $queryBuilder->getRootAliases()[0] ?? 'o';
        $aliases = [];
        $conditions = [];
        foreach ($fields as $field) {
            $parts = explode('.', $field);
            $column = (string) array_pop($parts);
            $alias = $rootAlias;
            $path = '';
            foreach ($parts as $association) {
                $path .= '.'.$association;
                if (!isset($aliases[$path])) {
                    $aliases[$path] = $queryNameGenerator->generateJoinAlias($association);
                    $queryBuilder->leftJoin($alias.'.'.$association, $aliases[$path]);
                }
                $alias = $aliases[$path];
            }
            $conditions[] = sprintf('LOWER(%s.%s) LIKE LOWER(:%s)', $alias, $column, self::PARAMETR);
        }
        $this->logger->info('Search for: "'.$stringValue.'"');
        $queryBuilder->andWhere('('.implode(' OR ', $conditions).')');
        $queryBuilder->setParameter(self::PARAMETR, '%'.addcslashes($stringValue, '%_\\').'%');
    }

    /**
     * Pole k hledání: z atributu (jen platná — cesta přes asociace na pole entity), jinak výchozí popisná.
     *
     * @param class-string $resourceClass
     *
     * @return list<string>
     */
    private function poleKHledani(string $resourceClass, QueryBuilder $queryBuilder): array
    {
        $em = $queryBuilder->getEntityManager();
        $annotation = self::readSearchAttribute($resourceClass);
        if (null === $annotation || [] === $annotation->fields) {
            $metadata = $em->getClassMetadata($resourceClass);

            return array_values(array_filter(self::VYCHOZI_POLE, static fn (string $field): bool => $metadata->hasField($field)));
        }
        $fields = [];
        foreach ($annotation->fields as $field) {
            $metadata = $em->getClassMetadata($resourceClass);
            $parts = explode('.', $field);
            $column = (string) array_pop($parts);
            $valid = true;
            foreach ($parts as $association) {
                if (!$metadata->hasAssociation($association)) {
                    $valid = false;
                    break;
                }
                $metadata = $em->getClassMetadata($metadata->getAssociationTargetClass($association));
            }
            if ($valid && $metadata->hasField($column)) {
                $fields[] = $field;
            } else {
                $this->logger->warning(sprintf('Search: pole „%s" v #[SearchAnnotation] u %s neexistuje — přeskočeno.', $field, $resourceClass));
            }
        }

        return $fields;
    }

    /**
     * @param class-string $resourceClass
     * @throws ReflectionException
     */
    private static function readSearchAttribute(string $resourceClass): ?SearchAnnotation
    {
        $reflection = new ReflectionClass($resourceClass);
        $attributes = $reflection->getAttributes(SearchAnnotation::class);
        if ([] === $attributes) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    public static function mixedToString(mixed $mixed): string
    {
        if ($mixed === null) {
            return '';
        }
        if (is_scalar($mixed)) {
            return (string)$mixed;
        }
        if (is_object($mixed) && method_exists($mixed, '__toString')) {
            return (string)$mixed;
        }
        if (is_array($mixed)) {
            return 'Array';
        }
        if (is_resource($mixed)) {
            return 'Resource';
        }

        return '';
    }
}
