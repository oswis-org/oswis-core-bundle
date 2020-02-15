<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Interfaces;

use DateTime;
use DateTimeInterface;

interface TimestampableInterface
{
    /**
     * Get date and time of entity creation.
     */
    public function getCreatedDateTime(): DateTime;

    /**
     * Get date and time of entity update.
     */
    public function getUpdatedDateTime(): DateTime;

    /**
     * Set date and time of entity update.
     */
    public function setUpdatedDateTime(DateTimeInterface $updatedDateTime): void;

    public function getCreatedDaysAgo(?bool $decimal = false): int;

    public function getUpdatedDaysAgo(?bool $decimal = false): int;
}
