<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Interfaces\AddressBook;

use DateTime;
use Exception;

interface AgeRangeInterface
{
    public function getMinAge(): int;

    public function setMinAge(?int $minAge): void;

    public function getMaxAge(): int;

    public function setMaxAge(?int $maxAge): void;

    public function getRangeInYears(): int;

    /**
     * True if person belongs to this age range (at some moment - referenceDateTime).
     *
     * @param  DateTime  $birthDate  BirthDate for age calculation
     * @param  DateTime|null  $referenceDateTime  Reference date, default is _now_
     *
     * @return bool True if belongs to age range
     *
     * @throws Exception
     */
    public function containsAccommodatedPerson(DateTime $birthDate, DateTime $referenceDateTime = null): bool;
}
