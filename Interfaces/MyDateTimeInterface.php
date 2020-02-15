<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Interfaces;

use DateTime;
use DateTimeInterface;

interface MyDateTimeInterface
{
    public function getDateTime(): ?DateTime;

    public function setDateTime(?DateTimeInterface $dateTime): void;

    public function getDaysAgo(?bool $decimal): ?int;
}
