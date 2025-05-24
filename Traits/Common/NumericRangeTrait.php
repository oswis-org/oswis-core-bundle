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

trait NumericRangeTrait
{
    /** Numeric minimum. */
    #[Column(type: 'integer', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(RangeFilter::class)]
    #[ApiFilter(NumericFilter::class)]
    protected ?int $min = null;

    /** Numeric maximum. */
    #[Column(type: 'integer', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(RangeFilter::class)]
    #[ApiFilter(NumericFilter::class)]
    protected ?int $max = null;

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function setMin(?int $min): void
    {
        $this->min = $min;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(?int $max): void
    {
        $this->max = $max;
    }

    public function betweenMinMax(?int $value): bool
    {
        if (null === $value) {
            return false;
        }

        return $value >= $this->min && $value <= $this->max;
    }
}
