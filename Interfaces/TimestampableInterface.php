<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Interfaces;

use DateTime;

interface TimestampableInterface
{
    public function getCreatedDateTime(): ?DateTime;

    public function getUpdatedDateTime(): ?DateTime;

    public function getCreatedDaysAgo(): ?int;

    public function getUpdatedDaysAgo(): ?int;
}
