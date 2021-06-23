<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use DateTime;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\DateTimeRange;
use OswisOrg\OswisCoreBundle\Utils\DateTimeUtils;

/**
 * Trait adds startDateTime and endDateTime fields and some other functions.
 */
trait DateRangeTrait
{
    /**
     * Date and time of range start.
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?DateTime $startDateTime = null;

    /**
     * Date and time of range end.
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
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
        return DateTimeUtils::getLength($this->getStartDate(), $this->getEndDateTime(), $type ?? DateTimeUtils::DATE_TIME_HOURS);
    }

    public function getStartDate(): ?DateTime
    {
        return $this->getStartDateTime();
    }

    public function getRangeAsText(bool $withoutYear = false): ?string
    {
        if (null === $this->getStartDate()) {
            return null;
        }
        if ($this->isInOnePeriod(DateTimeUtils::DATE_TIME_DAYS)) {
            return $this->getRangeAsTextDays($withoutYear);
        }
        if ($this->isInOnePeriod(DateTimeUtils::DATE_TIME_MONTHS)) {
            return $this->getRangeAsTextMonths($withoutYear);
        }
        if ($this->isInOnePeriod(DateTimeUtils::DATE_TIME_YEARS)) {
            return $this->getRangeAsTextYears($withoutYear);
        }
        $from = $this->getStartByFormat('\o\d j. n.'.($withoutYear ? '' : ' Y'));
        $to = $this->getEndByFormat(' \d\o  j. n.'.($withoutYear ? '' : ' Y'));

        return trim($from.$to);
    }

    public function isInOnePeriod(?string $period = null): ?bool
    {
        return DateTimeUtils::isInOnePeriod($period, $this->getStartDate(), $this->getEndDate());
    }

    public function getEndDate(): ?DateTime
    {
        return $this->getEndDateTime();
    }

    public function getRangeAsTextDays(?bool $withoutYear = false): ?string
    {
        return $this->getStartByFormat($withoutYear ? 'j. n.' : 'j. n. Y');
    }

    public function getStartByFormat(string $format = 'Y-m-d\TH:i:sP'): ?string
    {
        return $this->getStartDate()?->format($format);
    }

    public function getRangeAsTextMonths(?bool $withoutYear = false): ?string
    {
        return $this->getStartByFormat('j. ').$this->getEndByFormat($withoutYear ? '\až j. n.' : '\až j. n. Y');
    }

    public function getEndByFormat(string $format = 'Y-m-d\TH:i:sP'): ?string
    {
        return $this->getEndDate()?->format($format);
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
