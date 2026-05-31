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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use function count;
use function in_array;

/**
 * @noinspection ClassNameCollisionInspection
 */
final class SearchFilter extends AbstractFilter
{
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
     * @param mixed $value
     *
     * @throws ReflectionException
     * @throws BadRequestHttpException
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
        $stringValue = self::mixedToString($value);
        // Skip the unindexed multi-column LOWER(...) LIKE '%term%' scan (with auto
        // leftJoins) for too-short terms: a single character matches almost every
        // row across every searched field/join — the worst-case full scan. Search
        // for terms of >= 2 characters is unchanged.
        if (mb_strlen(trim($stringValue)) < 2) {
            return;
        }
        $this->logger->info('Search for: "'.$stringValue.'"');
        $annotation = self::readSearchAttribute($resourceClass);
        if (null === $annotation || [] === $annotation->fields) {
            throw new BadRequestHttpException('No Search implemented.');
        }
        $parameterName = $queryNameGenerator->generateParameterName($property);
        $search = [];
        $mappedJoins = [];
        foreach ($annotation->fields as $field) {
            $joins = explode('.', $field);
            for ($lastAlias = 'o', $i = 0, $num = count($joins); $i < $num; ++$i) {
                $currentAlias = $joins[$i];
                $currentAliasRenamed = $joins[$i].'_'.$i;
                if ($i === $num - 1) {
                    $search[] = "LOWER($lastAlias.$currentAlias) LIKE LOWER(:$parameterName)";
                }
                if ($i !== $num - 1) {
                    $join = "$lastAlias.$currentAlias";
                    if (!in_array($join, $mappedJoins, true)) {
                        $queryBuilder->leftJoin($join, $currentAliasRenamed);
                        $mappedJoins[] = $join;
                    }
                }
                $lastAlias = $currentAliasRenamed;
            }
        }
        $queryBuilder->andWhere(implode(' OR ', $search));
        $queryBuilder->setParameter($parameterName, '%'.$stringValue.'%');
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
