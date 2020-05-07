<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use DateTime;

interface MyDateTimeInterface
{
    public function getDateTime(): ?DateTime;

    public function setDateTime(?DateTime $dateTime): void;

    public function getDaysAgo(?bool $decimal): ?float;
}
