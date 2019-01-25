<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Zakjakub\OswisCoreBundle\Utils\DateTimeUtils;

/**
 * Trait ReservationDateRangeTrait
 * @package Zakjakub\OswisCoreBundle\Traits\Entity
 */
trait ReservationDateRangeTrait
{

    /**
     * Date and time of range start
     *
     * @var \DateTime
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected $startReservationDateTime;

    /**
     * Date and time of range end
     *
     * @var \DateTime
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected $endReservationDateTime;

    /**
     * @return \DateTime
     */
    final public function getStartReservationDateTime(): ?\DateTime
    {
        return $this->startReservationDateTime;
    }

    /**
     * @param \DateTime $startReservationDateTime
     */
    final public function setStartReservationDateTime(?\DateTime $startReservationDateTime): void
    {
        $this->startReservationDateTime = $startReservationDateTime;
    }

    /**
     * True if datetime belongs to this datetime range.
     *
     * @param \DateTime $dateTime Checked date and time
     *
     * @return bool True if belongs to date range
     * @throws \Exception
     */
    final public function containsReservationDateTime(\DateTime $dateTime): bool
    {
        return DateTimeUtils::isDateTimeInRange($this->startReservationDateTime, $this->getEndReservationDateTime(), $dateTime);
    }

    /**
     * @return \DateTime
     */
    final public function getEndReservationDateTime(): ?\DateTime
    {
        return $this->endReservationDateTime;
    }

    /**
     * @param \DateTime $endReservationDateTime
     */
    final public function setEndReservationDateTime(?\DateTime $endReservationDateTime): void
    {
        $this->endReservationDateTime = $endReservationDateTime;
    }

}
