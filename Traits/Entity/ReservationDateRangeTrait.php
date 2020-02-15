<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTimeInterface;
use Exception;
use Zakjakub\OswisCoreBundle\Utils\DateTimeUtils;

/**
 * Trait ReservationDateRangeTrait.
 */
trait ReservationDateRangeTrait
{
    /**
     * Date and time of range start.
     *
     * @var DateTimeInterface
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected DateTimeInterface $startReservationDateTime;

    /**
     * Date and time of range end.
     * @var DateTimeInterface
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected DateTimeInterface $endReservationDateTime;

    public function getStartReservationDateTime(): ?DateTimeInterface
    {
        return $this->startReservationDateTime;
    }

    public function setStartReservationDateTime(?DateTimeInterface $startReservationDateTime): void
    {
        $this->startReservationDateTime = $startReservationDateTime;
    }

    /**
     * True if datetime belongs to this datetime range.
     *
     * @param DateTimeInterface $dateTime Checked date and time
     *
     * @return bool True if belongs to date range
     * @throws Exception
     */
    public function containsReservationDateTime(DateTimeInterface $dateTime): bool
    {
        return DateTimeUtils::isDateTimeInRange($this->startReservationDateTime, $this->getEndReservationDateTime(), $dateTime);
    }

    public function getEndReservationDateTime(): ?DateTimeInterface
    {
        return $this->endReservationDateTime;
    }

    public function setEndReservationDateTime(?DateTimeInterface $endReservationDateTime): void
    {
        $this->endReservationDateTime = $endReservationDateTime;
    }
}
