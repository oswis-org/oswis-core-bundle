<?php

/**
 * @noinspection PhpUnused
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
 * Trait adds numericValue field.
 */
trait NumericValueTrait
{
    /** Numeric value (positive or negative). */
    #[Column(type: 'integer', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(RangeFilter::class)]
    #[ApiFilter(NumericFilter::class)]
    protected ?int $numericValue = null;

    /** Get numeric value. */
    public function getNumericValue(): ?int
    {
        return $this->numericValue;
    }

    /** Set numeric value. */
    public function setNumericValue(?int $numericValue): void
    {
        $this->numericValue = $numericValue;
    }
}
