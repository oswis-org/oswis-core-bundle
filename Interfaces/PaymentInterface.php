<?php

namespace Zakjakub\OswisCoreBundle\Interfaces;

/**
 * Interface PaymentInterface
 * @package App\Interfaces
 */
interface PaymentInterface extends DateTimeInterface, TimestampableInterface
{

    /**
     * Get value of payment (CZK).
     * @return int
     */
    public function getPaymentValue(): int;

    /**
     * Set value of payment (CZK).
     *
     * @param int|null $value
     */
    public function setPaymentValue(?int $value): void;
}
