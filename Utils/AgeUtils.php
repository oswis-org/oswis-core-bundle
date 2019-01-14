<?php

namespace Zakjakub\OswisResourcesBundle\Utils;

/**
 * Class AgeUtils
 * @package OswisResources
 * @author  Jakub Zak <mail@jakubzak.eu>
 */
class AgeUtils
{

    /**
     * True if person belongs to age range (at some moment - referenceDateTime).
     *
     * @param \DateTime      $birthDate         BirthDate for age calculation
     * @param int            $minAge            Minimal age, included (default is 0)
     * @param int            $maxAge            maximal age, included (default is infinity)
     * @param \DateTime|null $referenceDateTime Reference date, default is _now_
     *
     * @return bool True if belongs to age range
     * @throws \Exception
     */
    public static function isBirthDateInRange(
        \DateTime $birthDate,
        int $minAge = null,
        int $maxAge = null,
        \DateTime $referenceDateTime = null
    ): bool {
        $referenceDateTime = $referenceDateTime ?? new \DateTime();
        $age = self::getAgeFromBirthDate($birthDate, $referenceDateTime);
        $min = $minAge ?? 0;
        $max = $maxAge ?? \PHP_INT_MAX;

        return $age >= $min && $age <= $max;
    }

    /**
     * @param \DateTime      $birthDate
     * @param \DateTime|null $referenceDateTime
     *
     * @return int
     * @throws \Exception
     */
    public static function getAgeFromBirthDate(\DateTime $birthDate, \DateTime $referenceDateTime = null): int
    {
        return \floor(self::getAgeDecimalFromBirthDate($birthDate, $referenceDateTime));
    }

    /**
     * @param \DateTime      $birthDate
     * @param \DateTime|null $referenceDateTime
     *
     * @return int
     * @throws \Exception
     */
    public static function getAgeDecimalFromBirthDate(\DateTime $birthDate, ?\DateTime $referenceDateTime = null): int
    {
        $referenceDateTime = $referenceDateTime ?? new \DateTime();
        $referenceDateTime = new \DateTime($referenceDateTime->getTimestamp());
        $referenceDateTime->setTime(0, 0);
        $birthDate = new \DateTime($birthDate->getTimestamp());
        $birthDate->setTime(0, 0);

        return $birthDate->diff($referenceDateTime);
    }
}
