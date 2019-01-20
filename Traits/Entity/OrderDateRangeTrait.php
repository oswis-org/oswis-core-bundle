<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Zakjakub\OswisCoreBundle\Utils\DateTimeUtils;

/**
 * Trait adds createdDateTime and updatedDateTime fields
 *
 * Trait adds fields *createdDateTime* and *updatedDateTime* and allows to access them.
 * * _**createdDateTime**_ contains date and time when entity was created
 * * _**updatedDateTime**_ contains date and time when entity was updated/changed
 *
 */
trait OrderDateRangeTrait
{

    /**
     * Date and time of range start
     *
     * @var \DateTime
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected $startOrderDateTime;

    /**
     * Date and time of range end
     *
     * @var \DateTime
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected $endOrderDateTime;

    /**
     * @return \DateTime
     */
    final public function getStartOrderDateTime(): ?\DateTime
    {
        return $this->startOrderDateTime;
    }

    /**
     * @param \DateTime $startOrderDateTime
     */
    final public function setStartOrderDateTime(?\DateTime $startOrderDateTime): void
    {
        $this->startOrderDateTime = $startOrderDateTime;
    }

    /**
     * True if datetime belongs to this datetime range.
     *
     * @param \DateTime $dateTime Checked date and time
     *
     * @return bool True if belongs to date range
     * @throws \Exception
     */
    final public function containsOrderDateTime(\DateTime $dateTime): bool
    {
        return DateTimeUtils::isDateTimeInRange($this->startOrderDateTime, $this->getEndOrderDateTime(), $dateTime);
    }

    /**
     * @return \DateTime
     */
    final public function getEndOrderDateTime(): ?\DateTime
    {
        return $this->endOrderDateTime;
    }

    /**
     * @param \DateTime $endOrderDateTime
     */
    final public function setEndOrderDateTime(?\DateTime $endOrderDateTime): void
    {
        $this->endOrderDateTime = $endOrderDateTime;
    }

}
