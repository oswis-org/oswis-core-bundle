<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace Zakjakub\OswisCoreBundle\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\QueryBuilder;
use HttpInvalidParamException;
use ReflectionClass;
use ReflectionException;
use function count;
use function in_array;

/** @noinspection ClassNameCollisionInspection */

final class SearchFilter extends AbstractContextAwareFilter
{
    /**
     * @throws AnnotationException
     * @throws ReflectionException
     */
    public function getDescription(string $resourceClass): array
    {
        $reader = new AnnotationReader();
        $annotation = $reader->getClassAnnotation(new ReflectionClass(new $resourceClass()), SearchAnnotation::class);
        $description['search'] = [
            'property' => 'search',
            'type'     => 'string',
            'required' => false,
            'swagger'  => ['description' => 'FullTextFilter on '.implode(', ', $annotation ? $annotation->fields : [])],
        ];

        return $description;
    }

    /**
     * @param $value
     *
     * @throws AnnotationException
     * @throws ReflectionException
     * @throws HttpInvalidParamException
     * @noinspection MissingParameterTypeDeclarationInspection
     * @noinspection ForeachInvariantsInspection
     */
    public function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        if ('search' === $property) {
            $this->logger->info('Search for: '.$value);
        } else {
            return;
        }
        $reader = new AnnotationReader();
        $annotation = $reader->getClassAnnotation(
            new ReflectionClass(new $resourceClass()),
            SearchAnnotation::class
        );
        if (!$annotation) {
            throw new HttpInvalidParamException('No Search implemented.');
        }
        $parameterName = $queryNameGenerator->generateParameterName($property);
        $search = [];
        $mappedJoins = [];
        foreach ($annotation->fields as $field) {
            $joins = explode('.', $field);
            // @noinspection ForeachInvariantsInspection
            for ($lastAlias = 'o', $i = 0, $num = count($joins); $i < $num; ++$i) {
                $currentAlias = $joins[$i];
                $currentAliasRenamed = $joins[$i].'_'.$i;
                // $currentAlias = $joins[$i];
                if ($i === $num - 1) {
                    $search[] = "LOWER({$lastAlias}.{$currentAlias}) LIKE LOWER(:{$parameterName})";
                } else {
                    $join = "{$lastAlias}.{$currentAlias}";
                    if (!in_array($join, $mappedJoins, true)) {
                        $queryBuilder->leftJoin($join, $currentAliasRenamed);
                        $mappedJoins[] = $join;
                    }
                }
                $lastAlias = $currentAliasRenamed;
            }
        }
        $queryBuilder->andWhere(implode(' OR ', $search));
        $queryBuilder->setParameter($parameterName, '%'.$value.'%');
        // \error_log('DQL: ' . $queryBuilder->getDQL());
    }
}
