<?php /** @noinspection PhpUnused */

/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait EntityDeletedContainerTrait
{
    final public function setDeleted(?DateTime $deletedDateTime): void
    {
        if ($this->getDeleted() !== $deletedDateTime) {
            $newRevision = clone $this->getRevision();
            $newRevision->setDeleted($deletedDateTime);
            $this->addRevision($newRevision);
        }
    }

    final public function getDeleted(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getRevisionByDate($referenceDateTime)->getDeleted();
    }

    final public function isDeleted(?DateTime $referenceDateTime = null): bool
    {
        return $this->getRevisionByDate($referenceDateTime)->isDeleted();
    }

    final public function setEMailDeleteConfirmationDateTime(?DateTime $deletedConfirmationDateTime): void
    {
        if ($this->getEMailDeleteConfirmationDateTime() !== $deletedConfirmationDateTime) {
            $newRevision = clone $this->getRevision();
            $newRevision->setEMailDeleteConfirmationDateTime($deletedConfirmationDateTime);
            $this->addRevision($newRevision);
        }
    }

    final public function getEMailDeleteConfirmationDateTime(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getRevisionByDate($referenceDateTime)->getEMailDeleteConfirmationDateTime();
    }

}
