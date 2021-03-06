<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Payment;

/**
 * Trait adds priority field.
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
    protected ?int $taxRate = null;

    /**
     * Get tax rate (in percents).
     *
     * Only positive or zero tax rates are allowed. Value -1 means that tax is not defined.
     *
     * @return int
     */
    public function getTaxRate(): ?int
    {
        return (null === $this->taxRate || $this->taxRate < 0) ? -1 : $this->taxRate;
    }

    /**
     * Set tax rate (in percents).
     *
     * Only positive or zero tax rates are allowed. Value -1 means that tax is not defined.
     */
    public function setTaxRate(int $taxRate): void
    {
        $this->taxRate = (null === $taxRate || $taxRate < 0) ? -1 : $taxRate;
    }
}
