<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

/**
 * Trait adds *id* and *customId* fields.
 */
trait IdTrait
{
    /** Unique (auto-incremented) numeric identifier. */
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer')]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    #[ApiFilter(OrderFilter::class, properties: ['id' => 'ASC'])]
    #[ApiFilter(NumericFilter::class)]
    #[ApiFilter(RangeFilter::class)]
    protected ?int $id = null;

    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    #[ApiFilter(OrderFilter::class)]
    #[Column(type: 'string', length: 170, unique: true, nullable: true)]
    protected ?string $customId = null;

    public function getCustomId(): ?string
    {
        return $this->customId;
    }

    public function setCustomId(?bool $auto = true, ?string $customId = null): void
    {
        $this->customId = $auto ? $this->getAutoCustomId() : $customId;
    }

    public function getAutoCustomId(): string
    {
        return ''.$this->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
