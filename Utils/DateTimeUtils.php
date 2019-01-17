<?php

namespace Zakjakub\OswisCoreBundle\Utils;

/**
 * Class DateTimeUtils
 * @package OswisResources
 * @author  Jakub Zak <mail@jakubzak.eu>
 */
class DateTimeUtils
{

    /**
     * @param \DateInterval $dateInterval
     *
     * @return string
     */
    public static function formatIntervalToReadable(?\DateInterval $dateInterval): string
    {
        $result = '';
        if (!$dateInterval) {
            return $result;
        }
        if ($dateInterval->y) {
            $result .= $dateInterval->format('%y let ');
        }
        if ($dateInterval->m) {
            $result .= $dateInterval->format('%m měsíců ');
        }
        if ($dateInterval->d) {
            $result .= $dateInterval->format('%d dní ');
        }
        if ($dateInterval->h) {
            $result .= $dateInterval->format('%h hodin ');
        }
        if ($dateInterval->i) {
            $result .= $dateInterval->format('%i minut ');
        }
        if ($dateInterval->s) {
            $result .= $dateInterval->format('%s vteřin ');
        }

        return $result;
    }

    /**
     * True if datetime belongs to this datetime range.
     *
     * @param \DateTime      $start    Start of range
     * @param \DateTime      $end      End of range
     * @param \DateTime|null $dateTime Checked date and time
     *
     * @return bool True if belongs to date range
     * @throws \Exception
     */
    public static function isDateTimeInRange(?\DateTime $start, ?\DateTime $end, ?\DateTime $dateTime = null): bool
    {
        $start = $start ?? new \DateTime('1990-01-01');
        $end = $end ?? new \DateTime('2025-01-01');

        return $dateTime >= $start && $dateTime <= $end;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return bool
     */
    public static function isWeekend(\DateTime $dateTime): bool
    {
        return $dateTime->format('N') > 6;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return bool
     */
    public static function isPublicHolidays(
        /** @noinspection PhpUnusedParameterInspection */
        \DateTime $dateTime
    ): bool {
        /// TODO: Return true only on public holidays.
        return false;
    }

    public static function cmpDate(\DateTime $a, \DateTime $b): int
    {
        if ($a == $b) {
            return 0;
        }

        return $a < $b ? -1 : 1;
    }
}
