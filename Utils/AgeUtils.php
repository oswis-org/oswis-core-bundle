<?php

namespace Zakjakub\OswisCoreBundle\Utils;

use DateTime;
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
        ?DateTime $birthDate,
        int $minAge = null,
        int $maxAge = null,
        DateTime $referenceDateTime = null
    ): bool {
        if (null === $birthDate) {
            return false;
        }
        $referenceDateTime = $referenceDateTime ?? new DateTime();
        $age = self::getAgeFromBirthDate($birthDate, $referenceDateTime);
        $min = $minAge ?? 0;
        $max = $maxAge ?? PHP_INT_MAX;

        return $age >= $min && $age <= $max;
    }

    /**
     * @param DateTime $birthDate
     *
     * @return int
     *
     * @throws Exception
     */
    public static function getAgeFromBirthDate(?DateTime $birthDate, DateTime $referenceDateTime = null): ?int
    {
        if (!$birthDate) {
            return null;
        }

        return floor(self::getAgeDecimalFromBirthDate($birthDate, $referenceDateTime));
    }

    /**
     * @param DateTime $birthDate
     *
     * @return int
     *
     * @throws Exception
     */
    public static function getAgeDecimalFromBirthDate(?DateTime $birthDate, ?DateTime $referenceDateTime = null): ?int
    {
        if (!$birthDate) {
            return null;
        }
        $referenceDateTime = $referenceDateTime ?? new DateTime();
        $referenceDateTime->setTime(0, 0);
        $birthDate->setTime(0, 0);

        /// TODO: Return decimal!
        return $birthDate->diff($referenceDateTime)->y;
    }
}
