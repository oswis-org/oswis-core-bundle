<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 * @noinspection PhpUndefinedMethodInspection
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait BirthDateContainerTrait
{
    public function setBirthDate(?DateTime $birthDate): void
    {
        if ($this->getBirthDate() !== $birthDate) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setBirthDate($birthDate);
            $this->addRevision($newRevision);
        }
    }

    public function getBirthDate(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getBirthDate();
    }

    public function getAge(DateTime $dateTime, DateTime $referenceDateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getAge($referenceDateTime);
    }

    public function getAgeDecimal(DateTime $dateTime, DateTime $referenceDateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getAge($referenceDateTime);
    }
}
