<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Exception;
use Zakjakub\OswisCoreBundle\Utils\DateTimeUtils;

/**
 * Trait adds startDateTime and endDateTime fields and some other functions.
 */
trait DateRangeTrait
{

    /**
     * Date and time of range start
     *
     * @var DateTime
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected $startDateTime;

    /**
     * Date and time of range end
     *
     * @var DateTime
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected $endDateTime;

    /**
     * True if datetime belongs to this datetime range.
     *
     * @param DateTime $dateTime Checked date and time
     *
     * @return bool True if belongs to date range
     */
    final public function containsDateTimeInRange(?DateTime $dateTime = null): bool
    {
        try {
            $dateTime = $dateTime ?? new DateTime();

            return DateTimeUtils::isDateTimeInRange($this->getStartDateTime(), $this->getEndDateTime(), $dateTime);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return DateTime
     */
    final public function getStartDateTime(): ?DateTime
    {
        return $this->startDateTime;
    }

    /**
     * @param DateTime $startDateTime
     */
    final public function setStartDateTime(?DateTime $startDateTime): void
    {
        $this->startDateTime = $startDateTime ?? null;
    }

    /**
     * @return DateTime
     */
    final public function getEndDateTime(): ?DateTime
    {
        return $this->endDateTime;
    }

    /**
     * @param DateTime $endDateTime
     */
    final public function setEndDateTime(?DateTime $endDateTime): void
    {
        $this->endDateTime = $endDateTime ?? null;
    }

    /**
     * @param DateTime|null $dateTime
     */
    final public function setStartDate(?DateTime $dateTime): void
    {
        $this->setStartDateTime($dateTime);
    }

    /**
     * @param DateTime|null $dateTime
     */
    final public function setEndDate(?DateTime $dateTime): void
    {
        $this->setEndDateTime($dateTime);
    }

    final public function getLengthInHours(): ?int
    {
        if (!$this->getStartDate() || !$this->getEndDate()) {
            return null;
        }

        return (int)$this->getEndDate()->diff($this->getStartDate())->h;
    }

    /**
     * @return DateTime|null
     */
    final public function getStartDate(): ?DateTime
    {
        return $this->getStartDateTime();
    }

    /**
     * @return DateTime|null
     */
    final public function getEndDate(): ?DateTime
    {
        return $this->getStartDateTime();
    }

}
