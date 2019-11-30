<?php /** @noinspection PhpUnused */

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
    final public function containsDateTimeInRange(?DateTime $dateTime = null, ?DateTime $referenceDateTime = null): bool
    {
        $dateTime = $dateTime ?? new DateTime();
        $start = $this->getStartDateTime($referenceDateTime);
        $end = $this->getEndDateTime($referenceDateTime);

        return DateTimeUtils::isDateTimeInRange($start, $end, $dateTime);
    }

    /**
     * @return DateTime
     */
    final public function getStartDateTime(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getRevisionByDate($referenceDateTime)->getStartDateTime();
    }

    /**
     * @return DateTime
     */
    final public function getEndDateTime(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getRevisionByDate($referenceDateTime)->getEndDateTime();
    }

    final public function getStartDate(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getStartDateTime($referenceDateTime);
    }

    final public function getEndDate(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getEndDateTime($referenceDateTime);
    }

    final public function setStartDate(?DateTime $dateTime): void
    {
        $this->setStartDateTime($dateTime);
    }

    /**
     * @param DateTime $startDateTime
     */
    final public function setStartDateTime(?DateTime $startDateTime): void
    {
        if ($this->getStartDateTime() !== $startDateTime) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setStartDateTime($startDateTime);
            $this->addRevision($newRevision);
        }
    }

    final public function setEndDate(?DateTime $dateTime): void
    {
        $this->setEndDateTime($dateTime);
    }

    /**
     * @param DateTime $endDateTime
     */
    final public function setEndDateTime(?DateTime $endDateTime): void
    {
        if ($this->getEndDateTime() !== $endDateTime) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setEndDateTime($endDateTime);
            $this->addRevision($newRevision);
        }
    }

    final public function getLengthInHours(?DateTime $referenceDateTime = null): ?int
    {
        return $this->getRevisionByDate($referenceDateTime)->getLengthInHours();
    }
}
