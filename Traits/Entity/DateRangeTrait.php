<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Entity;

use DateTime;
use OswisOrg\OswisCoreBundle\Utils\DateTimeUtils;

/**
 * Trait adds startDateTime and endDateTime fields and some other functions.
 */
trait DateRangeTrait
{
    /**
     * Date and time of range start.
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected ?DateTime $startDateTime = null;

    /**
     * Date and time of range end.
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected ?DateTime $endDateTime = null;

    /**
     * True if datetime belongs to this datetime range.
     *
     * @param DateTime $dateTime Checked date and time ('now' if not set).
     *
     * @return bool True if belongs to date range.
     */
    public function containsDateTimeInRange(?DateTime $dateTime = null): bool
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
     * @param DateTime $endDateTime
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
        return DateTimeUtils::getLength($this->getStartDate(), $this->getEndDateTime(), $type);
    }

    public function getStartDate(): ?DateTime
    {
        return $this->getStartDateTime();
    }

    public function getRangeAsText(bool $withoutYear = true): ?string
    {
        if (null === $this->getStartDate()) {
            return null;
        }
        if ($this->isInOnePeriod(DateTimeUtils::DATE_TIME_DAYS)) {
            return $this->getStartByFormat($withoutYear ? 'j. n.' : 'j. n. Y');
        }
        if ($this->isInOnePeriod(DateTimeUtils::DATE_TIME_MONTHS)) { // TODO: až => amž - escape it!!
            return $this->getStartByFormat('j. ').$this->getEndByFormat($withoutYear ? '\až j. n.' : '\až j. n. Y');
        }
        if ($this->isInOnePeriod(DateTimeUtils::DATE_TIME_YEARS)) {
            return $this->getStartByFormat('j. n. ').$this->getEndByFormat($withoutYear ? '\až j. n.' : '\až j. n. Y');
        }

        return $this->getStartByFormat($withoutYear ? 'j. n.' : 'j. n. Y').$this->getEndByFormat($withoutYear ? ' \až j. n.' : ' \až j. n. Y');
    }

    public function isInOnePeriod(string $period): ?bool
    {
        return DateTimeUtils::isInOnePeriod($period, $this->getStartDate(), $this->getEndDate());
    }

    public function getEndDate(): ?DateTime
    {
        return $this->getEndDateTime();
    }

    public function getStartByFormat(string $format): ?string
    {
        return $this->getStartDate() ? $this->getStartDate()
            ->format($format) : null;
    }

    public function getEndByFormat(string $format): ?string
    {
        return $this->getEndDate() ? $this->getEndDate()
            ->format($format) : null;
    }
}
