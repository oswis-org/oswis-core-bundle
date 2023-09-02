<?php

/**
 * @noinspection RealpathInStreamContextInspection
 * @noinspection NestedTernaryOperatorInspection
 * @noinspection MissingParameterTypeDeclarationInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Twig\Extension;

use DateTime;
use DateTimeZone;
use Exception;
use OswisOrg\OswisCoreBundle\Utils\DateTimeUtils;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class DateRangeExtension extends AbstractExtension
{
    public function __construct(private readonly Environment $twig)
    {
    }

    public function getFunctions(): array
    {
        return [new TwigFunction('date_range_string', [$this, 'dateRangeString'])];
    }

    public function getFilters(): array
    {
        return [new TwigFilter('date_range_string', [$this, 'dateRangeString'])];
    }

    public function dateRangeString(
        DateTime|string|null $start,
        DateTime|string|null $end,
        bool                 $withoutYear = false,
    ): string
    {
        $start = empty($start) ? null : $start;
        $end = empty($end) ? null : $end;
        try {
            $start = is_string($start) ? new DateTime($start) : $start;
        } catch (Exception) {
            $start = null;
        }
        try {
            $end = is_string($end) ? new DateTime($end) : $end;
        } catch (Exception) {
            $end = null;
        }
        $globals = $this->twig->getGlobals();
        $timezoneString = $globals['timezone'];
        $timezone = new DateTimeZone('' . $timezoneString);
        $start = $start?->setTimezone($timezone);
        $end = $end?->setTimezone($timezone);
        if (null === $start && null === $end) {
            return '';
        }
        if ($start && DateTimeUtils::isInOnePeriod(DateTimeUtils::DATE_TIME_DAYS, $start, $end)) {
            return $this->getRangeAsTextDays($start, $end, $withoutYear, true) ?? '';
        }
        if ($start && DateTimeUtils::isInOnePeriod(DateTimeUtils::DATE_TIME_MONTHS, $start, $end)) {
            return $this->getRangeAsTextMonths($start, $end, $withoutYear) ?? '';
        }
        if ($start && DateTimeUtils::isInOnePeriod(DateTimeUtils::DATE_TIME_YEARS, $start, $end)) {
            return $this->getRangeAsTextYears($start, $end, $withoutYear) ?? '';
        }
        $from = $this->getStartByFormat($start, '\o\d j. n.'.($withoutYear ? '' : ' Y'));
        $to = $this->getEndByFormat($end, ' \d\o  j. n.'.($withoutYear ? '' : ' Y'));

        return trim($from.$to);
    }

    public function getRangeAsTextDays(
        ?DateTime $start,
        ?DateTime $end,
        ?bool $withoutYear = false,
        ?bool $includeTime = false,
    ): ?string {
        $result = $this->getStartByFormat($start, $withoutYear ? 'j. n.' : 'j. n. Y');
        if ($includeTime) {
            $result .= ' '.$this->getRangeAsTextHours($start, $end);
        }

        return $result;
    }

    public function getStartByFormat(
        ?DateTime $start,
        string $format = 'Y-m-d\TH:i:sP',
    ): ?string {
        return $start?->format($format);
    }

    public function getRangeAsTextHours(
        ?DateTime $start,
        ?DateTime $end,
        ?bool $includeSecons = false,
    ): ?string {
        $format = 'H:i'.($includeSecons ? ':s' : '');

        return $this->getStartByFormat($start, $format).'–'.$this->getEndByFormat($end, $format);
    }

    public function getEndByFormat(
        ?DateTime $end,
        string $format = 'Y-m-d\TH:i:sP',
    ): ?string {
        return $end?->format($format);
    }

    public function getRangeAsTextMonths(
        ?DateTime $start,
        ?DateTime $end,
        ?bool $withoutYear = false,
    ): ?string {
        return $this->getStartByFormat($start, 'j. ').$this->getEndByFormat($end,
                $withoutYear ? '\až j. n.' : '\až j. n. Y');
    }

    public function getRangeAsTextYears(
        ?DateTime $start,
        ?DateTime $end,
        ?bool $withoutYear = false,
    ): ?string {
        return $this->getStartByFormat($start, 'j. n. ').$this->getEndByFormat($end,
                $withoutYear ? '\až j. n.' : '\až j. n. Y');
    }


}
