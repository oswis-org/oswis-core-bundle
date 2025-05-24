<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

trait FloatRangeTrait
{
    /** Minimal value. */
    #[Column(type: 'float', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(ExistsFilter::class)]
    protected ?float $min = null;

    /** Maximal value. */
    #[Column(type: 'float', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(DateFilter::class)]
    #[ApiFilter(ExistsFilter::class)]
    protected ?float $max = null;

    public function getMin(): ?float
    {
        return $this->min;
    }

    public function setMin(?float $min): void
    {
        $this->min = $min;
    }

    public function getMax(): ?float
    {
        return $this->max;
    }

    public function setMax(?float $max): void
    {
        $this->max = $max;
    }

    public function betweenMinMax(?float $value): bool
    {
        if (null === $value) {
            return false;
        }

        return $value >= $this->min && $value <= $this->max;
    }
}
