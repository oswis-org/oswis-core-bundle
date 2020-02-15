<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Interfaces;

use DateTimeInterface;
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
     * @param DateTimeInterface      $birthDate         BirthDate for age calculation
     * @param DateTimeInterface|null $referenceDateTime Reference date, default is _now_
     *
     * @return bool True if belongs to age range
     *
     * @throws Exception
     */
    public function containsAccommodatedPerson(DateTimeInterface $birthDate, DateTimeInterface $referenceDateTime = null): bool;
}
