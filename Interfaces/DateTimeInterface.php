<?php

namespace Zakjakub\OswisCoreBundle\Interfaces;

use DateTime;

interface DateTimeInterface
{
    public function getDateTime(): ?DateTime;

    public function setDateTime(?DateTime $dateTime): void;

    public function getDaysAgo(?bool $decimal): ?int;
}
