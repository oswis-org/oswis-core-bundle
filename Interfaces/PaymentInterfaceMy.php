<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Interfaces;

/**
 * Interface PaymentInterface.
 */
interface PaymentInterfaceMy extends MyDateTimeInterface, TimestampableInterface
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
