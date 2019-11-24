<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait NumericRangeTrait
{

    /**
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $min;

    /**
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $max;

    /**
     * @return int|null
     */
    final public function getMin(): ?int
    {
        return $this->min;
    }

    /**
     * @param int|null $min
     */
    final public function setMin(?int $min): void
    {
        $this->min = $min;
    }

    /**
     * @return int|null
     */
    final public function getMax(): ?int
    {
        return $this->max;
    }

    /**
     * @param int|null $max
     */
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
