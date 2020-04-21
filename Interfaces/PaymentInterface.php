<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Interfaces;

/**
 * Interface PaymentInterface.
 */
interface PaymentInterface extends MyDateTimeInterface, TimestampableInterface
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
