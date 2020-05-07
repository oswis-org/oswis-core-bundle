<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

trait NumericRangeTrait
{
    /**
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $min = null;

    /**
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
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
