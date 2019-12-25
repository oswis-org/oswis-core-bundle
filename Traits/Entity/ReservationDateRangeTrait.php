<?php /** @noinspection MethodShouldBeFinalInspection */

/** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
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
     * @var DateTime
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected DateTime $startReservationDateTime;

    /**
     * Date and time of range end.
     *
     * @var DateTime
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected DateTime $endReservationDateTime;

    /**
     * @return DateTime
     */
    public function getStartReservationDateTime(): ?DateTime
    {
        return $this->startReservationDateTime;
    }

    /**
     * @param DateTime $startReservationDateTime
     */
    public function setStartReservationDateTime(?DateTime $startReservationDateTime): void
    {
        $this->startReservationDateTime = $startReservationDateTime;
    }

    /**
     * True if datetime belongs to this datetime range.
     *
     * @param DateTime $dateTime Checked date and time
     *
     * @return bool True if belongs to date range
     * @throws Exception
     */
    public function containsReservationDateTime(DateTime $dateTime): bool
    {
        return DateTimeUtils::isDateTimeInRange($this->startReservationDateTime, $this->getEndReservationDateTime(), $dateTime);
    }

    /**
     * @return DateTime
     */
    public function getEndReservationDateTime(): ?DateTime
    {
        return $this->endReservationDateTime;
    }

    /**
     * @param DateTime $endReservationDateTime
     */
    public function setEndReservationDateTime(?DateTime $endReservationDateTime): void
    {
        $this->endReservationDateTime = $endReservationDateTime;
    }
}
