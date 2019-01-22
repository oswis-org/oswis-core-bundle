<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait PriceListTrait
{
    use NameableBasicTrait;
    use DateTimeTrait;

    /**
     * Percentage (as decimal 0.01-1.00) from reservation charge (excludes resort fees) needed as deposit.
     * @var int
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected $depositPercentage;

    /**
     * Percentage (as decimal 0.01-1.00) from reservation charge (excludes resort fees) needed as cancel fee.
     * @var int
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected $cancelFeePercentage;

    /**
     * Working days available for payment of deposit.
     * @var int
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected $depositDaysAfterOrder;

    /**
     * Minimal amount of working days before reservation start for complete payment.
     * @var int
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected $restDaysBeforeReservation;

    /**
     * Minimal length of stay in nights
     * @var int
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected $minLength;

    /**
     * @return int
     */
    final public function getDepositPercentage(): ?int
    {
        return $this->depositPercentage;
    }

    /**
     * @param int $depositPercentage
     */
    final public function setDepositPercentage(?int $depositPercentage): void
    {
        $this->depositPercentage = $depositPercentage;
    }

    /**
     * @return int
     */
    final public function getCancelFeePercentage(): ?int
    {
        return $this->cancelFeePercentage;
    }

    /**
     * @param int $cancelFeePercentage
     */
    final public function setCancelFeePercentage(?int $cancelFeePercentage): void
    {
        $this->cancelFeePercentage = $cancelFeePercentage;
    }

    /**
     * @return int
     */
    final public function getDepositDaysAfterOrder(): ?int
    {
        return $this->depositDaysAfterOrder;
    }

    /**
     * @param int $depositDaysAfterOrder
     */
    final public function setDepositDaysAfterOrder(int $depositDaysAfterOrder): void
    {
        $this->depositDaysAfterOrder = $depositDaysAfterOrder;
    }

    /**
     * @return int
     */
    final public function getRestDaysBeforeReservation(): ?int
    {
        return $this->restDaysBeforeReservation;
    }

    /**
     * @param int $restDaysBeforeReservation
     */
    final public function setRestDaysBeforeReservation(?int $restDaysBeforeReservation): void
    {
        $this->restDaysBeforeReservation = $restDaysBeforeReservation;
    }

    /**
     * @return int
     */
    final public function getMinLength(): int
    {
        return $this->minLength ?? 0;
    }

    /**
     * @param int $minLength
     */
    final public function setMinLength(?int $minLength): void
    {
        $this->minLength = $minLength;
    }
}
