<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds priority field
 */
trait TaxRateTrait
{

    /**
     * Tax rate (in percents).
     *
     * @var int|null
     *
     * @Doctrine\ORM\Mapping\Column(nullable=false, options={"default": 0})
     */
    private $taxRate;

    /**
     * Get tax rate (in percents).
     *
     * @return int
     */
    final public function getTaxRate(): int
    {
        return $this->taxRate ?? 0;
    }

    /**
     * Set tax rate (in percents).
     *
     * @param int $taxRate
     */
    final public function setTaxRate(int $taxRate): void
    {
        $this->taxRate = $taxRate ?? 0;
    }
}
