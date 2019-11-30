<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

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

    final public function getMin(): ?int
    {
        return $this->min;
    }

    final public function setMin(?int $min): void
    {
        $this->min = $min;
    }

    final public function getMax(): ?int
    {
        return $this->max;
    }

    final public function setMax(?int $max): void
    {
        $this->max = $max;
    }

    final public function betweenMinMax(?int $value): bool
    {
        if (null === $value) {
            return false;
        }

        return $value >= $this->min && $value <= $this->max;
    }
}
