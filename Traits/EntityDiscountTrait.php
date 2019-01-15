<?php

namespace Zakjakub\OswisResourcesBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait EntityDiscountTrait
 */
trait EntityDiscountTrait
{
    use EntityNameableBasicTrait;
    use EntityAgeRangeTrait;
    use EntityOrderDateRangeTrait;
    use EntityReservationDateRangeTrait;

    /**
     * Maximal amount of persons to use this discount.
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $maximalPersonsAmount;

    /**
     * Maximal amount of nights to use this discount.
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $maximalNightsAmount;

    /**
     * Amount of discounts available (negative is infinity, 1 is not reusable, 0 is not usable).
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $amountAvailable;

    /**
     * @return int
     */
    final public function getMaximalPersonsAmount(): int
    {
        return $this->maximalPersonsAmount;
    }

    /**
     * @param int $maximalPersonsAmount
     */
    final public function setMaximalPersonsAmount(int $maximalPersonsAmount): void
    {
        $this->maximalPersonsAmount = $maximalPersonsAmount;
    }

    /**
     * @return int
     */
    final public function getMaximalNightsAmount(): int
    {
        return $this->maximalNightsAmount;
    }

    /**
     * @param int $maximalNightsAmount
     */
    final public function setMaximalNightsAmount(int $maximalNightsAmount): void
    {
        $this->maximalNightsAmount = $maximalNightsAmount;
    }

    /**
     * Get amount of discounts available (negative is infinity, 1 is not reusable, 0 not usable).
     * @return int Amount of discounts available (0 is infinity, 1 is not reusable, negative is not usable)
     */
    final public function getAmountAvailable(): int
    {
        return $this->amountAvailable;
    }

    /**
     * Set amount of discounts available (negative is infinity, 1 is not reusable, 0 is not usable).
     *
     * @param int $amountAvailable Amount of discounts available (0 is infinity, 1 is not reusable, negative is not usable)
     */
    final public function setAmountAvailable(int $amountAvailable): void
    {
        $this->amountAvailable = $amountAvailable;
    }
}