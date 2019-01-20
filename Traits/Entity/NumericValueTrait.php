<?php

namespace Zakjakub\OswisCoreBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds numericValue field
 */
trait NumericValueTrait
{

    /**
     * Numeric value.
     *
     * @var int|null
     *
     * @ORM\Column(nullable=false, options={"default": 0})
     */
    private $numericValue;

    /**
     * Get numeric value.
     *
     * @return int
     */
    final public function getNumericValue(): int
    {
        return $this->numericValue ?? 0;
    }

    /**
     * Set numeric value.
     *
     * @param int $numericValue
     */
    final public function setNumericValue(int $numericValue): void
    {
        $this->numericValue = $numericValue ?? 0;
    }
}
