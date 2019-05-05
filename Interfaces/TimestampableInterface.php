<?php

namespace Zakjakub\OswisCoreBundle\Interfaces;

use DateTime;

interface TimestampableInterface
{

    /**
     * Get date and time of entity creation
     *
     * @return DateTime
     */
    public function getCreatedDateTime(): DateTime;

    /**
     * Get date and time of entity update
     *
     * @return DateTime
     */
    public function getUpdatedDateTime(): DateTime;

    /**
     * Set date and time of entity update
     *
     * @param DateTime $updatedDateTime
     */
    public function setUpdatedDateTime(DateTime $updatedDateTime): void;

    public function getCreatedDaysAgo(?bool $decimal = false): int;

    public function getUpdatedDaysAgo(?bool $decimal = false): int;
}
