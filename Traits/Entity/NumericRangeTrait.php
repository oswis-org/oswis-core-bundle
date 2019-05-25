<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait NumericRangeTrait
{

    /**
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected $min;

    /**
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected $max;

    /**
     * @return int
     */
    final public function getMin(): ?int
    {
        return $this->min;
    }

    /**
     * @param int $min
     */
    final public function setMin(?int $min): void
    {
        $this->min = $min;
    }

    /**
     * @return int
     */
    final public function getMax(): ?int
    {
        return $this->max;
    }

    /**
     * @param int $max
     */
    final public function setMax(?int $max): void
    {
        $this->max = $max;
    }

    final public function betweenMinMax(int $value): bool
    {
        return $value >= $this->min && $value <= $this->max;
    }
}
