<?php

namespace Zakjakub\OswisCoreBundle\Utils;

/**
 * Class DateTimeUtils
 * @package OswisCoreBundle
 * @author  Jakub Zak <mail@jakubzak.eu>
 */
class DateTimeUtils
{

    public const MIN_DATE_TIME_STRING = '1970-01-01 00:00:00';
    public const MAX_DATE_TIME_STRING = '2038-01-19 00:00:00';

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
        $start = $start ?? new \DateTime(self::MIN_DATE_TIME_STRING);
        $end = $end ?? new \DateTime(self::MAX_DATE_TIME_STRING);

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

    public static function isPublicHolidays(\DateTime $dateTime): bool
    {
        return self::getPublicHolidays($dateTime) ? true : false;
    }

    public static function getPublicHolidays(\DateTime $dateTime): ?string
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
        if (\array_key_exists($m, $publicHolidays) && \array_key_exists($d, $publicHolidays[$m])) {
            return $publicHolidays[$m][$d] ?? null;
        }

        return null;
    }

    public static function getEaster(\DateTime $dateTime): string
    {
        $y = (int)$dateTime->format('Y');
        if ($dateTime === strtotime('+1 day', easter_date($y))) {
            return 'Velikonoční pondělí';
        }
        if ($dateTime === strtotime('-2 day', easter_date($y))) {
            return 'Velký pátek';
        }

        return null;
    }

    public static function isEaster(\DateTime $dateTime): bool
    {
        return self::getEaster($dateTime) ? true : false;
    }

    public static function cmpDate(\DateTime $a, \DateTime $b): int
    {
        if ($a == $b) {
            return 0;
        }

        return $a < $b ? -1 : 1;
    }


}
