<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use DateTime;

interface MyDateTimeInterface
{
    public function getDateTime(): ?DateTime;

    public function setDateTime(?DateTime $dateTime): void;

    public function getDaysAgo(?bool $decimal): ?float;
}
