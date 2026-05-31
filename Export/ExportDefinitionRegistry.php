<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Export;

final class ExportDefinitionRegistry
{
    /** @var array<string, ExportDefinitionInterface> */
    private array $byKey = [];

    /** @var array<class-string, ExportDefinitionInterface> */
    private array $byResource = [];

    /**
     * @param iterable<ExportDefinitionInterface> $definitions
     */
    public function __construct(iterable $definitions)
    {
        foreach ($definitions as $definition) {
            $this->byKey[$definition->getKey()] = $definition;
            $this->byResource[$definition->getResourceClass()] = $definition;
        }
    }

    public function getByKey(string $key): ?ExportDefinitionInterface
    {
        return $this->byKey[$key] ?? null;
    }

    /**
     * @param class-string $resourceClass
     */
    public function getByResourceClass(string $resourceClass): ?ExportDefinitionInterface
    {
        return $this->byResource[$resourceClass] ?? null;
    }
}
