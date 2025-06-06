<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use DateTime;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\DateTimeRange;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;
use OswisOrg\OswisCoreBundle\Utils\DateTimeUtils;

/**
 * Trait adds startDateTime and endDateTime fields and some other functions.
 */
trait DateRangeTrait
{
    /** Date and time of range start. */
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(DateFilter::class)]
    #[ApiFilter(ExistsFilter::class)]
    #[Column(type: 'datetime', nullable: true, options: ['default' => null])]
    protected ?DateTime $startDateTime = null;

    /** Date and time of range end. */
    #[Column(type: 'datetime', nullable: true, options: ['default' => null])]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(DateFilter::class)]
    #[ApiFilter(ExistsFilter::class)]
    protected ?DateTime $endDateTime = null;

    /**
     * True if datetime belongs to this datetime range.
     *
     * @param  DateTime  $dateTime  Checked date and time ('now' if not set).
     *
     * @return bool True if belongs to date range.
     */
    public function isInDateRange(?DateTime $dateTime = null): bool
    {
        return DateTimeUtils::isDateTimeInRange($this->getStartDateTime(), $this->getEndDateTime(), $dateTime);
    }

    public function getStartDateTime(): ?DateTime
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(?DateTime $startDateTime): void
    {
        $this->startDateTime = $startDateTime;
    }

    /**
     * @return DateTime
     */
    public function getEndDateTime(): ?DateTime
    {
        return $this->endDateTime;
    }

    /**
     * @param  DateTime  $endDateTime
     */
    public function setEndDateTime(?DateTime $endDateTime): void
    {
        $this->endDateTime = $endDateTime;
    }

    public function setStartDate(?DateTime $dateTime): void
    {
        $this->setStartDateTime($dateTime);
    }

    public function setEndDate(?DateTime $dateTime): void
    {
        $this->setEndDateTime($dateTime);
    }

    public function getLength(?string $type = DateTimeUtils::DATE_TIME_HOURS): ?int
    {
        return DateTimeUtils::getLength($this->getStartDate(), $this->getEndDateTime(),
            $type ?? DateTimeUtils::DATE_TIME_HOURS);
    }

    public function getStartDate(): ?DateTime
    {
        return $this->getStartDateTime();
    }

    public function getRangeAsText(bool $withoutYear = false): ?string
    {
        if (null === $this->getStartDate() && null === $this->getEndDate()) {
            return null;
        }
        if ($this->getStartDate() && $this->isInOnePeriod(DateTimeUtils::DATE_TIME_DAYS)) {
            return $this->getRangeAsTextDays($withoutYear, true);
        }
        if ($this->getStartDate() && $this->isInOnePeriod(DateTimeUtils::DATE_TIME_MONTHS)) {
            return $this->getRangeAsTextMonths($withoutYear);
        }
        if ($this->getStartDate() && $this->isInOnePeriod(DateTimeUtils::DATE_TIME_YEARS)) {
            return $this->getRangeAsTextYears($withoutYear);
        }
        $from = $this->getStartByFormat('\o\d j. n.'.($withoutYear ? '' : ' Y'));
        $to = $this->getEndByFormat(' \d\o  j. n.'.($withoutYear ? '' : ' Y'));

        return trim($from.$to);
    }

    public function getEndDate(): ?DateTime
    {
        return $this->getEndDateTime();
    }

    public function isInOnePeriod(?string $period = null): ?bool
    {
        return DateTimeUtils::isInOnePeriod($period, $this->getStartDate(), $this->getEndDate());
    }

    public function getRangeAsTextDays(?bool $withoutYear = false, ?bool $includeTime = false): ?string
    {
        $result = $this->getStartByFormat($withoutYear ? 'j. n.' : 'j. n. Y');
        if ($includeTime) {
            $result .= ' '.$this->getRangeAsTextHours();
        }

        return $result;
    }

    public function getStartByFormat(string $format = 'Y-m-d\TH:i:sP'): ?string
    {
        return $this->getStartDate()?->format($format);
    }

    public function getRangeAsTextHours(?bool $includeSecons = false): ?string
    {
        $format = 'H:i'.($includeSecons ? ':s' : '');

        return $this->getStartByFormat($format).'–'.$this->getEndByFormat($format);
    }

    public function getEndByFormat(string $format = 'Y-m-d\TH:i:sP'): ?string
    {
        return $this->getEndDate()?->format($format);
    }

    public function getRangeAsTextMonths(?bool $withoutYear = false): ?string
    {
        return $this->getStartByFormat('j. ').$this->getEndByFormat($withoutYear ? '\až j. n.' : '\až j. n. Y');
    }

    public function getRangeAsTextYears(?bool $withoutYear = false): ?string
    {
        return $this->getStartByFormat('j. n. ').$this->getEndByFormat($withoutYear ? '\až j. n.' : '\až j. n. Y');
    }

    public function setDateTimeRange(?DateTimeRange $dateTimeRange = null): void
    {
        if (null !== $dateTimeRange) {
            $this->setStartDateTime($dateTimeRange->startDateTime);
            $this->setEndDateTime($dateTimeRange->endDateTime);
        }
    }

    public function getDateTimeRange(): DateTimeRange
    {
        return new DateTimeRange($this->getStartDateTime(), $this->getEndDateTime());
    }
}
