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
     * Only positive or zero tax rates are allowed. Value -1 means that tax is not defined.
     *
     * @var int|null
     *
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     */
    private $taxRate;

    /**
     * Get tax rate (in percents).
     *
     * Only positive or zero tax rates are allowed. Value -1 means that tax is not defined.
     * @return int
     */
    final public function getTaxRate(): ?int
    {
        return ($this->taxRate === null or $this->taxRate < 0) ? -1 : $this->taxRate;
    }

    /**
     * Set tax rate (in percents).
     *
     * Only positive or zero tax rates are allowed. Value -1 means that tax is not defined.
     *
     * @param int $taxRate
     */
    final public function setTaxRate(int $taxRate): void
    {
        $this->taxRate = ($taxRate === null or $taxRate < 0) ? -1 : $taxRate;
    }
}
