<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
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

    /**
     * @return DateTime
     */
    public function getStartDateTime(): ?DateTime
    {
        return $this->startDateTime;
    }

    /**
     * @param DateTime $startDateTime
     */
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

    public function getLength(?string $type = DateTimeUtils::LENGTH_TYPE_HOURS): ?int
    {
        return DateTimeUtils::getLength($this->getStartDate(), $this->getEndDateTime(), $type);
    }

    public function getStartDate(): ?DateTime
    {
        return $this->getStartDateTime();
    }

    public function getEndDate(): ?DateTime
    {
        return $this->getStartDateTime();
    }
}
