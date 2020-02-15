<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use DateTimeInterface;
use Zakjakub\OswisCoreBundle\Utils\DateTimeUtils;

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
    protected ?DateTimeInterface $startDateTime = null;

    /**
     * Date and time of range end.
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected ?DateTimeInterface $endDateTime = null;

    /**
     * True if datetime belongs to this datetime range.
     *
     * @param DateTimeInterface $dateTime Checked date and time ('now' if not set).
     *
     * @return bool True if belongs to date range.
     */
    public function containsDateTimeInRange(?DateTimeInterface $dateTime = null): bool
    {
        return DateTimeUtils::isDateTimeInRange($this->getStartDateTime(), $this->getEndDateTime(), $dateTime);
    }

    public function getStartDateTime(): ?DateTimeInterface
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(?DateTimeInterface $startDateTime): void
    {
        $this->startDateTime = $startDateTime;
    }

    /**
     * @return DateTime
     */
    public function getEndDateTime(): ?DateTimeInterface
    {
        return $this->endDateTime;
    }

    /**
     * @param DateTimeInterface $endDateTime
     */
    public function setEndDateTime(?DateTimeInterface $endDateTime): void
    {
        $this->endDateTime = $endDateTime;
    }

    public function setStartDate(?DateTimeInterface $dateTime): void
    {
        $this->setStartDateTime($dateTime);
    }

    public function setEndDate(?DateTimeInterface $dateTime): void
    {
        $this->setEndDateTime($dateTime);
    }

    public function getLength(?string $type = DateTimeUtils::DATE_TIME_HOURS): ?int
    {
        return DateTimeUtils::getLength($this->getStartDate(), $this->getEndDateTime(), $type);
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->getStartDateTime();
    }

    public function getRangeAsText(): ?string
    {
        if (null === $this->getStartDate()) {
            return null;
        }
        if ($this->isInOnePeriod(DateTimeUtils::DATE_TIME_DAYS)) {
            return $this->getStartByFormat('j. n. Y');
        }
        if ($this->isInOnePeriod(DateTimeUtils::DATE_TIME_MONTHS)) { // TODO: až => amž - escape it!!
            return $this->getStartByFormat('j. ').$this->getEndByFormat('\až j. n. Y');
        }
        if ($this->isInOnePeriod(DateTimeUtils::DATE_TIME_YEARS)) {
            return $this->getStartByFormat('j. n. ').$this->getEndByFormat('\až j. n. Y');
        }

        return $this->getStartByFormat('j. n. Y').$this->getEndByFormat(' \až j. n. Y');
    }

    public function isInOnePeriod(string $period): ?bool
    {
        return DateTimeUtils::isInOnePeriod($period, $this->getStartDate(), $this->getEndDate());
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->getEndDateTime();
    }

    public function getStartByFormat(string $format): ?string
    {
        return $this->getStartDate() ? $this->getStartDate()->format($format) : null;
    }

    public function getEndByFormat(string $format): ?string
    {
        return $this->getEndDate() ? $this->getEndDate()->format($format) : null;
    }
}
