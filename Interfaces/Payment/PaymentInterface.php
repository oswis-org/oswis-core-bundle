<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Interfaces\Payment;

use OswisOrg\OswisCoreBundle\Interfaces\Common\BasicInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Common\MyDateTimeInterface;

/**
 * Interface PaymentInterface.
 */
interface PaymentInterface extends BasicInterface, MyDateTimeInterface
{
    public function getNumericValue(): ?int;

    public function setNumericValue(?int $numericValue): void;
    // TODO: Add missing methods.
}
