<?php

namespace Zakjakub\OswisCoreBundle\Utils;

use DateTime;
use DateTimeInterface;
use Exception;
use function floor;
use const PHP_INT_MAX;

/**
 * Class AgeUtils.
 *
 * @author  Jakub Zak <mail@jakubzak.eu>
 */
class AgeUtils
{
    /**
     * True if person belongs to age range (at some moment - referenceDateTime).
     *
     * @param DateTime      $birthDate         BirthDate for age calculation
     * @param int           $minAge            Minimal age, included (default is 0)
     * @param int           $maxAge            maximal age, included (default is infinity)
     * @param DateTime|null $referenceDateTime Reference date, default is _now_
     *
     * @return bool True if birth date belongs to age range interval.
     *
     * @throws Exception
     */
    public static function isBirthDateInRange(
        ?DateTimeInterface $birthDate,
        int $minAge = null,
        int $maxAge = null,
        DateTimeInterface $referenceDateTime = null
    ): bool {
        if (null === $birthDate) {
            return false;
        }
        $referenceDateTime ??= new DateTime();
        $age = self::getAgeFromBirthDate($birthDate, $referenceDateTime);

        return $age >= ($minAge ?? 0) && $age <= ($maxAge ?? PHP_INT_MAX);
    }

    /**
     * @throws Exception
     */
    public static function getAgeFromBirthDate(?DateTimeInterface $birthDate, DateTimeInterface $referenceDateTime = null): ?int
    {
        return $birthDate ? (int)floor(self::getAgeDecimalFromBirthDate($birthDate, $referenceDateTime)) : null;
    }

    /**
     * @param DateTimeInterface $birthDate
     *
     * @return int
     * @throws Exception
     */
    public static function getAgeDecimalFromBirthDate(?DateTimeInterface $birthDate, ?DateTimeInterface $referenceDateTime = null): ?int
    {
        if (!($birthDate instanceof DateTime)) {
            return null;
        }
        $referenceDateTime ??= new DateTime();
        assert($referenceDateTime instanceof DateTime);
        $referenceDateTime->setTime(0, 0);
        $birthDate->setTime(0, 0);

        /// TODO: Return decimal!
        return $birthDate->diff($referenceDateTime)->y;
    }
}
