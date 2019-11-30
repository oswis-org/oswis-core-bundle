<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Interfaces;

/**
 * Interface PaymentInterface.
 */
interface PaymentInterface extends DateTimeInterface, TimestampableInterface
{
    /**
     * Get value of payment (CZK).
     */
    public function getPaymentValue(): int;

    /**
     * Set value of payment (CZK).
     */
    public function setPaymentValue(?int $value): void;
}
