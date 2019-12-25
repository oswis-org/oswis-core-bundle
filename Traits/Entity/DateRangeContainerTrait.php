<?php /** @noinspection MethodShouldBeFinalInspection */
/** @noinspection MethodShouldBeFinalInspection */
/** @noinspection MethodShouldBeFinalInspection */
/** @noinspection ALL */
/** @noinspection PhpUnused */
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Exception;
use Zakjakub\OswisCoreBundle\Utils\DateTimeUtils;

/**
 * Trait adds startDateTime and endDateTime fields and some other functions.
 */
trait DateRangeContainerTrait
{
    /**
     * True if datetime belongs to this datetime range.
     *
     * @param DateTime $dateTime Checked date and time
     *
     * @return bool True if belongs to date range
     * @throws Exception
     */
    public function containsDateTimeInRange(?DateTime $dateTime = null, ?DateTime $referenceDateTime = null): bool
    {
        $dateTime = $dateTime ?? new DateTime();
        $start = $this->getStartDateTime($referenceDateTime);
        $end = $this->getEndDateTime($referenceDateTime);

        return DateTimeUtils::isDateTimeInRange($start, $end, $dateTime);
    }

    /**
     * @return DateTime
     */
    public function getStartDateTime(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getRevisionByDate($referenceDateTime)->getStartDateTime();
    }

    /**
     * @return DateTime
     */
    public function getEndDateTime(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getRevisionByDate($referenceDateTime)->getEndDateTime();
    }

    public function getStartDate(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getStartDateTime($referenceDateTime);
    }

    public function getEndDate(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getEndDateTime($referenceDateTime);
    }

    public function setStartDate(?DateTime $dateTime): void
    {
        $this->setStartDateTime($dateTime);
    }

    /**
     * @param DateTime $startDateTime
     */
    public function setStartDateTime(?DateTime $startDateTime): void
    {
        if ($this->getStartDateTime() !== $startDateTime) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setStartDateTime($startDateTime);
            $this->addRevision($newRevision);
        }
    }

    public function setEndDate(?DateTime $dateTime): void
    {
        $this->setEndDateTime($dateTime);
    }

    /**
     * @param DateTime $endDateTime
     */
    public function setEndDateTime(?DateTime $endDateTime): void
    {
        if ($this->getEndDateTime() !== $endDateTime) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setEndDateTime($endDateTime);
            $this->addRevision($newRevision);
        }
    }

    public function getLengthInHours(?DateTime $referenceDateTime = null): ?int
    {
        return $this->getRevisionByDate($referenceDateTime)->getLengthInHours();
    }
}
