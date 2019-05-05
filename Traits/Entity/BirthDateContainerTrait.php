<?php
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Exception;

trait BirthDateContainerTrait
{

    /**
     * @param DateTime|null $birthDate
     *
     * @throws RevisionMissingException
     */
    final public function setBirthDate(?DateTime $birthDate): void
    {
        if ($this->getBirthDate() !== $birthDate) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setBirthDate($birthDate);
            $this->addRevision($newRevision);
        }
    }

    final public function getBirthDate(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getBirthDate();
    }

    /**
     * @param DateTime      $dateTime
     * @param DateTime|null $referenceDateTime
     *
     * @return int|null
     */
    final public function getAge(DateTime $dateTime, DateTime $referenceDateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getAge($referenceDateTime);
    }

    /**
     * @param DateTime      $dateTime
     * @param DateTime|null $referenceDateTime
     *
     * @return int|null
     * @throws Exception
     */
    final public function getAgeDecimal(DateTime $dateTime, DateTime $referenceDateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getAge($referenceDateTime);
    }
}
