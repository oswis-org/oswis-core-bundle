<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait PriceListTrait
{
    use NameableBasicTrait;
    use DateTimeTrait;

    /**
     * Percentage (as decimal 0.01-1.00) from reservation charge (excludes resort fees) needed as deposit.
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $depositPercentage;

    /**
     * Percentage (as decimal 0.01-1.00) from reservation charge (excludes resort fees) needed as cancel fee.
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $cancelFeePercentage;

    /**
     * Working days available for payment of deposit.
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $depositDaysAfterOrder;

    /**
     * Minimal amount of working days before reservation start for complete payment.
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $restDaysBeforeReservation;

    /**
     * Minimal length of stay in nights
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $minLength;

    /**
     * @return int|null
     */
    final public function getDepositPercentage(): ?int
    {
        return $this->depositPercentage;
    }

    /**
     * @param int|null $depositPercentage
     */
    final public function setDepositPercentage(?int $depositPercentage): void
    {
        $this->depositPercentage = $depositPercentage;
    }

    /**
     * @return int|null
     */
    final public function getCancelFeePercentage(): ?int
    {
        return $this->cancelFeePercentage;
    }

    /**
     * @param int|null $cancelFeePercentage
     */
    final public function setCancelFeePercentage(?int $cancelFeePercentage): void
    {
        $this->cancelFeePercentage = $cancelFeePercentage;
    }

    /**
     * @return int|null
     */
    final public function getDepositDaysAfterOrder(): ?int
    {
        return $this->depositDaysAfterOrder;
    }

    /**
     * @param int|null $depositDaysAfterOrder
     */
    final public function setDepositDaysAfterOrder(?int $depositDaysAfterOrder): void
    {
        $this->depositDaysAfterOrder = $depositDaysAfterOrder;
    }

    /**
     * @return int|null
     */
    final public function getRestDaysBeforeReservation(): ?int
    {
        return $this->restDaysBeforeReservation;
    }

    /**
     * @param int|null $restDaysBeforeReservation
     */
    final public function setRestDaysBeforeReservation(?int $restDaysBeforeReservation): void
    {
        $this->restDaysBeforeReservation = $restDaysBeforeReservation;
    }

    /**
     * @return int|null
     */
    final public function getMinLength(): int
    {
        return $this->minLength ?? 0;
    }

    /**
     * @param int|null $minLength
     */
    final public function setMinLength(?int $minLength): void
    {
        $this->minLength = $minLength;
    }
}
