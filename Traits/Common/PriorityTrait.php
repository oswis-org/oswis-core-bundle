<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

/**
 * Trait adds priority field.
 */
trait PriorityTrait
{
    /** Priority (numeric, positive (higher) or negative (lower)). */
    #[Column(type: 'integer', nullable: true, options: ['default' => 0])]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(RangeFilter::class)]
    #[ApiFilter(NumericFilter::class)]
    protected ?int $priority = 0;

    /**
     * Get priority.
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * Set priority.
     *
     * @param  int  $priority
     */
    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
    }
}
