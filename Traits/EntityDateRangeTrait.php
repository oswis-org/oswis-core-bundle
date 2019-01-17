<?php

namespace Zakjakub\OswisCoreBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Zakjakub\OswisCoreBundle\Utils\DateTimeUtils;

/**
 * Trait adds createdDateTime and updatedDateTime fields
 *
 * Trait adds fields *createdDateTime* and *updatedDateTime* and allows to access them.
 * * _**createdDateTime**_ contains date and time when entity was created
 * * _**updatedDateTime**_ contains date and time when entity was updated/changed
 *
 */
trait EntityDateRangeTrait
{

    /**
     * Date and time of range start
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected $startDateTime;

    /**
     * Date and time of range end
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected $endDateTime;

    /**
     * True if datetime belongs to this datetime range.
     *
     * @param \DateTime $dateTime Checked date and time
     *
     * @return bool True if belongs to date range
     * @throws \Exception
     */
    final public function containsDateTimeInRange(?\DateTime $dateTime = null): bool
    {
        $dateTime = $dateTime ?? new \DateTime();

        return DateTimeUtils::isDateTimeInRange($this->getStartDateTime(), $this->getEndDateTime(), $dateTime);
    }

    /**
     * @return \DateTime
     */
    final public function getStartDateTime(): ?\DateTime
    {
        return $this->startDateTime ?? null;
    }

    /**
     * @param \DateTime $startDateTime
     */
    final public function setStartDateTime(?\DateTime $startDateTime): void
    {
        $this->startDateTime = $startDateTime ?? null;
    }

    /**
     * @return \DateTime
     */
    final public function getEndDateTime(): ?\DateTime
    {
        return $this->endDateTime ?? null;
    }

    /**
     * @param \DateTime $endDateTime
     */
    final public function setEndDateTime(?\DateTime $endDateTime): void
    {
        $this->endDateTime = $endDateTime ?? null;
    }
}
