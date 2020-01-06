<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 * @noinspection PhpUndefinedMethodInspection
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Exception;

/**
 * Trait adds getters and setters for container of entity with age range fields.
 */
trait AgeRangeContainerTrait
{
    /**
     * @param DateTime $birthDate
     * @param DateTime $referenceDateTime
     *
     * @throws Exception
     */
    public function containsBirthDate(?DateTime $birthDate, ?DateTime $referenceDateTime): bool
    {
        return $this->getRevisionByDate($referenceDateTime)->containsBirthDate($birthDate, $referenceDateTime);
    }

    public function agesDiff(?DateTime $referenceDateTime = null): int
    {
        return $this->getRevisionByDate($referenceDateTime)->containsBirthDate();
    }

    public function setMinAge(?int $minAge): void
    {
        if ($this->getMinAge() !== $minAge) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setMinAge($minAge);
            $this->addRevision($newRevision);
        }
    }

    public function getMinAge(?DateTime $dateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getMinAge();
    }

    public function setMaxAge(?int $maxAge): void
    {
        if ($this->getMaxAge() !== $maxAge) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setMaxAge($maxAge);
            $this->addRevision($newRevision);
        }
    }

    public function getMaxAge(?DateTime $dateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getMaxAge();
    }
}
