<?php /** @noinspection MethodShouldBeFinalInspection */

/** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait PriceListTrait
{
    use NameableBasicTrait;
    use DateTimeTrait;

    /**
     * Percentage (as decimal 0.01-1.00) from reservation charge (excludes resort fees) needed as deposit.
     *
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $depositPercentage;

    /**
     * Percentage (as decimal 0.01-1.00) from reservation charge (excludes resort fees) needed as cancel fee.
     *
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $cancelFeePercentage;

    /**
     * Working days available for payment of deposit.
     *
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $depositDaysAfterOrder;

    /**
     * Minimal amount of working days before reservation start for complete payment.
     *
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $restDaysBeforeReservation;

    /**
     * Minimal length of stay in nights.
     *
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $minLength = null;

    public function getDepositPercentage(): ?int
    {
        return $this->depositPercentage;
    }

    public function setDepositPercentage(?int $depositPercentage): void
    {
        $this->depositPercentage = $depositPercentage;
    }

    public function getCancelFeePercentage(): ?int
    {
        return $this->cancelFeePercentage;
    }

    public function setCancelFeePercentage(?int $cancelFeePercentage): void
    {
        $this->cancelFeePercentage = $cancelFeePercentage;
    }

    public function getDepositDaysAfterOrder(): ?int
    {
        return $this->depositDaysAfterOrder;
    }

    public function setDepositDaysAfterOrder(?int $depositDaysAfterOrder): void
    {
        $this->depositDaysAfterOrder = $depositDaysAfterOrder;
    }

    public function getRestDaysBeforeReservation(): ?int
    {
        return $this->restDaysBeforeReservation;
    }

    public function setRestDaysBeforeReservation(?int $restDaysBeforeReservation): void
    {
        $this->restDaysBeforeReservation = $restDaysBeforeReservation;
    }

    /**
     * @return int|null
     */
    public function getMinLength(): int
    {
        return $this->minLength ?? 0;
    }

    public function setMinLength(?int $minLength): void
    {
        $this->minLength = $minLength;
    }
}
