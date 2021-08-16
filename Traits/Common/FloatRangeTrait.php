<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

trait FloatRangeTrait
{
    /**
     * Minimal value.
     * @Doctrine\ORM\Mapping\Column(type="float", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="exact")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?float $min = null;

    /**
     * Maximal value.
     * @Doctrine\ORM\Mapping\Column(type="float", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="exact")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
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
