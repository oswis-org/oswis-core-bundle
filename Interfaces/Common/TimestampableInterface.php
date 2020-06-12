<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use DateTime;

interface TimestampableInterface
{
    public function getCreatedAt(): ?DateTime;

    public function getUpdatedAt(): ?DateTime;

    public function getCreatedDaysAgo(): ?int;

    public function getUpdatedDaysAgo(): ?int;
}
