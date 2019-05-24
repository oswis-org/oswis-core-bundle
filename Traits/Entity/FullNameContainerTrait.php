<?php
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait FullNameContainerTrait
{

    /**
     * @param string|null $fullName
     *
     * @throws RevisionMissingException
     */
    final public function setFullName(?string $fullName): void
    {
        if ($this->getFullName() !== $fullName) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setFullName($fullName);
            $this->addRevision($newRevision);
        }
    }

    final public function getFullName(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getFullName();
    }

    final public function getNickname(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getNickname();
    }

    final public function getGivenName(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getGivenName();
    }

    final public function getFamilyName(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getFamilyName();
    }

    final public function getSalutationName(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getSalutationName();
    }

    final public function getCzechSuffixA(?DateTime $dateTime = null): string
    {
        return $this->getRevisionByDate($dateTime)->getCzechSuffixA();
    }

}
