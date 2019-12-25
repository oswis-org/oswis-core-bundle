<?php /** @noinspection MethodShouldBeFinalInspection */

/** @noinspection PhpUnused */

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
     * Date and time of range start.
     *
     * @var DateTime|null
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected ?DateTime $startDateTime = null;

    /**
     * Date and time of range end.
     *
     * @var DateTime|null
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected ?DateTime $endDateTime = null;

    /**
     * True if datetime belongs to this datetime range.
     *
     * @param DateTime $dateTime Checked date and time
     *
     * @return bool True if belongs to date range
     */
    public function containsDateTimeInRange(?DateTime $dateTime = null): bool
    {
        try {
            return DateTimeUtils::isDateTimeInRange($this->getStartDateTime(), $this->getEndDateTime(), $dateTime ?? new DateTime());
        } catch (Exception $e) {
            return false;
        }
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

    public function getLengthInHours(): ?int
    {
        return (!$this->getStartDate() || !$this->getEndDate()) ? null : (int)$this->getEndDate()->diff($this->getStartDate())->h;
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
