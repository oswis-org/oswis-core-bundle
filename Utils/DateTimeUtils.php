<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Utils;

use DateInterval;
use DateTime;
use Exception;
use InvalidArgumentException;

use function array_key_exists;

/**
 * Utilities for work with DateTime objects.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
class DateTimeUtils
{
    public const RANGE_ALL = '';
    public const RANGE_YEAR = 'year';
    public const RANGE_MONTH = 'month';
    public const RANGE_WEEK = 'week';
    public const RANGE_DAY = 'day';

    public const MIN_DATE_TIME_STRING = '1970-01-01 00:00:00';
    public const MAX_DATE_TIME_STRING = '2038-01-19 00:00:00';

    public const DATE_TIME_SECONDS = 's';
    public const DATE_TIME_MINUTES = 'i';
    public const DATE_TIME_HOURS = 'h';
    public const DATE_TIME_DAYS = 'd';
    public const DATE_TIME_DAYS_ALL = 'days';
    public const DATE_TIME_MONTHS = 'm';
    public const DATE_TIME_YEARS = 'y';

    public const PERIOD_TYPES_ALLOWED
        = [
            self::DATE_TIME_HOURS,
            self::DATE_TIME_DAYS,
            self::DATE_TIME_MONTHS,
            self::DATE_TIME_YEARS,
        ];

    public const LENGTH_TYPES_ALLOWED
        = [
            ...self::PERIOD_TYPES_ALLOWED,
            self::DATE_TIME_SECONDS,
            self::DATE_TIME_MINUTES,
            self::DATE_TIME_DAYS_ALL,
        ];

    /**
     * @param  DateInterval  $dateInterval
     */
    public static function formatIntervalToReadable(?DateInterval $dateInterval): string
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
     * @param  DateTime  $start  Start of range.
     * @param  DateTime  $end  End of range.
     * @param  DateTime|null  $dateTime  Checked date and time ('now' if it's not set).
     *
     * @return bool True if belongs to date range.
     */
    public static function isDateTimeInRange(?DateTime $start, ?DateTime $end, ?DateTime $dateTime = null): bool
    {
        try {
            $dateTime ??= new DateTime();
            $start ??= new DateTime(self::MIN_DATE_TIME_STRING);
            $end ??= new DateTime(self::MAX_DATE_TIME_STRING);

            return $dateTime >= $start && $dateTime <= $end;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Converts DateTime to start (or end) of some range (year, month, day).
     *
     * @param  DateTime|null  $dateTime
     * @param  string|null  $range
     * @param  bool|null  $isEnd
     *
     * @return DateTime
     * @throws Exception
     */
    public static function getDateTimeByRange(?DateTime $dateTime, ?string $range, ?bool $isEnd = false): DateTime
    {
        if (empty($range)) {
            return self::getByRangeAll($dateTime, $isEnd);
        }
        if (in_array($range, [self::RANGE_YEAR, self::RANGE_MONTH, self::RANGE_DAY], true)) {
            $dateTime ??= new DateTime();
            $month = self::getMonthByRange($dateTime, $range, $isEnd);
            $day = self::getDayByRange($dateTime, $range, $isEnd);
            $minute = $isEnd ? 59 : 0;
            $dateTime->setDate((int)$dateTime->format('Y'), $month, $day);

            return $dateTime->setTime($isEnd ? 23 : 0, $minute, $minute, $isEnd ? 999 : 0);
        }
        throw new InvalidArgumentException("Rozsah '$range' není povolen.");
    }

    /**
     * @param  DateTime|null  $dateTime
     * @param  bool|null  $isEnd
     *
     * @return DateTime
     * @throws Exception
     * @noinspection PhpUndefinedClassInspection
     */
    private static function getByRangeAll(?DateTime $dateTime, ?bool $isEnd = false): DateTime
    {
        return $dateTime ?? new DateTime($isEnd ? self::MAX_DATE_TIME_STRING : self::MIN_DATE_TIME_STRING);
    }

    public static function getMonthByRange(DateTime $dateTime, ?string $range, ?bool $isEnd = false): int
    {
        $month = $isEnd ? 12 : 1;

        return ($range === self::RANGE_MONTH || $range === self::RANGE_DAY) ? (int)$dateTime->format('n') : $month;
    }

    public static function getDayByRange(DateTime $dateTime, ?string $range, ?bool $isEnd = false): int
    {
        $day = $isEnd ? (int)$dateTime->format('t') : 1;

        return ($range === self::RANGE_DAY) ? (int)$dateTime->format('j') : $day;
    }

    public static function isWeekend(DateTime $dateTime): bool
    {
        return $dateTime->format('N') > 6;
    }

    public static function isPublicHolidays(DateTime $dateTime): bool
    {
        return !empty(self::getPublicHolidays($dateTime));
    }

    public static function getPublicHolidays(DateTime $dateTime): ?string
    {
        $publicHolidays = [];
        $publicHolidays[1][1] = 'Den obnovy samostatného českého státu';
        $publicHolidays[5][1] = 'Svátek práce';
        $publicHolidays[5][8] = 'Den vítězství';
        $publicHolidays[7][5] = 'Den slovanských věrozvěstů Cyrila a Metoděje';
        $publicHolidays[7][6] = 'Den upálení mistra Jana Husa';
        $publicHolidays[9][28] = 'Den české státnosti';
        $publicHolidays[10][28] = 'Den vzniku samostatného československého státu';
        $publicHolidays[11][17] = 'Den boje za svobodu a demokracii';
        $publicHolidays[12][24] = 'Štědrý den';
        $publicHolidays[12][25] = '1. svátek vánoční';
        $publicHolidays[12][26] = '2. svátek vánoční';
        $easter = self::getEaster($dateTime);
        if ($easter) {
            return $easter;
        }
        // Public holidays in array [month][day].
        $m = (int)$dateTime->format('n');
        $d = (int)$dateTime->format('j');
        if (array_key_exists($m, $publicHolidays) && array_key_exists($d, $publicHolidays[$m])) {
            return $publicHolidays[$m][$d] ?? null;
        }

        return null;
    } // Counts all included days.

    public static function getEaster(DateTime $dateTime): ?string
    {
        $dateTime->setTime(0, 0);
        $y = (int)$dateTime->format('Y');
        if ($dateTime->getTimestamp() === strtotime('+1 day', easter_date($y))) {
            return 'Velikonoční pondělí';
        }
        if ($dateTime->getTimestamp() === strtotime('-2 day', easter_date($y))) {
            return 'Velký pátek';
        }

        return null;
    }

    public static function isEaster(DateTime $dateTime): bool
    {
        return (bool)self::getEaster($dateTime);
    }

    public static function cmpDate(?DateTime $a, ?DateTime $b): int
    {
        if ($a === $b) {
            return 0;
        }

        return $a < $b ? -1 : 1;
    }

    public static function getLength(?DateTime $start, ?DateTime $end, string $type = self::DATE_TIME_HOURS): ?int
    {
        if (null === $start || null === $end || !in_array($type, self::LENGTH_TYPES_ALLOWED, true)) {
            return null;
        }
        $interval = $end->diff($start);

        return empty($interval->invert) ? null : (int)$interval->$type;
    }

    public static function isInOnePeriod(?string $period, ?DateTime $start, ?DateTime $end): ?bool
    {
        if (empty($period) || null === $start || null === $end || !in_array($period, self::PERIOD_TYPES_ALLOWED, true)) {
            return null;
        }
        if ($period === self::DATE_TIME_YEARS) {
            $format = 'Y';
        }
        if ($period === self::DATE_TIME_MONTHS) {
            $format = 'Y-m';
        }
        if ($period === self::DATE_TIME_DAYS) {
            $format = 'Y-m-d';
        }
        if ($period === self::DATE_TIME_HOURS) {
            $format = 'Y-m-d H';
        }

        return !empty($format) && ($start->format($format) === $end->format($format));
    }
}
