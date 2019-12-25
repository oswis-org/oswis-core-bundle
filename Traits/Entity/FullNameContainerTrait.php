<?php /** @noinspection MethodShouldBeFinalInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait FullNameContainerTrait
{
    public function setFullName(?string $fullName): void
    {
        if ($this->getFullName() !== $fullName) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setFullName($fullName);
            $this->addRevision($newRevision);
        }
    }

    public function getFullName(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getFullName();
    }

    public function getNickname(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getNickname();
    }

    public function getGivenName(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getGivenName();
    }

    public function getFamilyName(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getFamilyName();
    }

    public function getSalutationName(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getSalutationName();
    }

    public function getCzechSuffixA(?DateTime $dateTime = null): string
    {
        return $this->getRevisionByDate($dateTime)->getCzechSuffixA();
    }
}
