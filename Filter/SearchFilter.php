<?php

/**
 * @noinspection PhpComposerExtensionStubsInspection
 * @noinspection MissingParameterTypeDeclarationInspection
 * @noinspection ForeachInvariantsInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\QueryBuilder;
use HttpInvalidParamException;
use ReflectionClass;
use ReflectionException;
use function count;
use function in_array;

/**
 * Search filter.
 * @noinspection ClassNameCollisionInspection
 */
final class SearchFilter extends AbstractFilter
{
    /**
     * @throws ReflectionException
     */
    public function getDescription(string $resourceClass): array
    {
        $reader = new AnnotationReader();
        $annotation = $reader->getClassAnnotation(new ReflectionClass(new $resourceClass()), SearchAnnotation::class);

        return [
            'search' => [
                'property' => 'search',
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'FullTextFilter on '.implode(
                            ', ',
                            $annotation instanceof SearchAnnotation ? $annotation->fields : []
                        ),
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     *
     * @throws ReflectionException
     * @throws HttpInvalidParamException
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
        $this->logger->info('Search for: "'.$stringValue.'"');
        $reader = new AnnotationReader();
        $annotation = $reader->getClassAnnotation(new ReflectionClass(new $resourceClass()), SearchAnnotation::class);
        if (empty($annotation)) {
            throw new HttpInvalidParamException('No Search implemented.');
        }
        $parameterName = $queryNameGenerator->generateParameterName($property);
        $search = [];
        $mappedJoins = [];
        foreach ($annotation->fields as $field) {
            $field = self::mixedToString($field);
            $joins = explode('.', $field);
            // @noinspection ForeachInvariantsInspection
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

    public static function mixedToString(mixed $mixed): string
    {
        if ($mixed === null) {
            return ""; // Convert null to an empty string
        }
        if (is_scalar($mixed)) {
            return (string)$mixed; // Convert scalar values to strings
        }
        if (is_object($mixed) && method_exists($mixed, '__toString')) {
            return (string)$mixed; // Use __toString method if available
        }
        if (is_array($mixed)) {
            return "Array"; // Handle arrays (you might want to serialize or handle differently) <sup data-citation="1"><a href="https://www.php.net/manual/en/language.types.string.php" target="_blank" title="Strings - Manual - PHP">1</a></sup>
        }
        if (is_resource($mixed)) {
            return "Resource"; // Handle resources <sup data-citation="1"><a href="https://www.php.net/manual/en/language.types.string.php" target="_blank" title="Strings - Manual - PHP">1</a></sup>
        }

        return ""; // Default to empty string for unknown types
    }
}
