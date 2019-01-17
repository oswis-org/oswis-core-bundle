<?php
/** @noinspection PhpDocRedundantThrowsInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits;

use Zakjakub\OswisCoreBundle\Exceptions\BirthDateMissingException;
use Zakjakub\OswisCoreBundle\Exceptions\RevisionMissingException;

/**
 * Trait adds getters and setters for container of entity with age range fields.
 */
trait EntityAgeRangeContainerTrait
{

    /**
     * @param \DateTime $birthDate
     * @param \DateTime $referenceDateTime
     *
     * @return bool
     * @throws \Exception
     */
    final public function containsBirthDate(?\DateTime $birthDate, ?\DateTime $referenceDateTime): bool
    {
        if (!$birthDate) {
            throw new BirthDateMissingException();
        }
        if (!$referenceDateTime) {
            $referenceDateTime = new \DateTime();
        }

        return $this->getRevisionByDate($referenceDateTime)->containsBirthDate($birthDate, $referenceDateTime);
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return int
     * @throws \Exception
     */
    final public function agesDiff(?\DateTime $dateTime = null): int
    {
        return $this->getMaxAge($dateTime) - $this->getMinAge($dateTime);
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return int|null
     * @throws RevisionMissingException
     */
    final public function getMaxAge(?\DateTime $dateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getMaxAge();
    }

    /**
     * @param \DateTime|null $dateTime
     *
     * @return int|null
     * @throws RevisionMissingException
     */
    final public function getMinAge(?\DateTime $dateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getMinAge();
    }

    /**
     * @param int|null $minAge
     *
     * @throws RevisionMissingException
     */
    final public function setMinAge(?int $minAge): void
    {
        if ($this->getMinAge() !== $minAge) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setMinAge($minAge);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @param int|null $maxAge
     *
     * @throws RevisionMissingException
     */
    final public function setMaxAge(?int $maxAge): void
    {
        if ($this->getMaxAge() !== $maxAge) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setMaxAge($maxAge);
            $this->addRevision($newRevision);
        }
    }
}
