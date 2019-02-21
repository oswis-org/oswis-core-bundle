<?php
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

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

    final public function getFullName(?\DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getFullName();
    }

    final public function getNickname(?\DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getNickname();
    }

}
