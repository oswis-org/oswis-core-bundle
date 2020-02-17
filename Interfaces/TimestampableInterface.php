<?php
/**
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Interfaces;

use DateTime;

interface TimestampableInterface
{
    public function getCreatedDateTime(): ?DateTime;

    public function getUpdatedDateTime(): ?DateTime;

    public function getCreatedDaysAgo(?bool $decimal = false): ?int;

    public function getUpdatedDaysAgo(?bool $decimal = false): ?int;
}
