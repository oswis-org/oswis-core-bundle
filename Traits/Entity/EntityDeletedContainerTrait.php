<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 * @noinspection PhpUndefinedMethodInspection
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait EntityDeletedContainerTrait
{
    public function setDeleted(?DateTime $deletedDateTime): void
    {
        if ($this->getDeleted() !== $deletedDateTime) {
            $newRevision = clone $this->getRevision();
            $newRevision->setDeleted($deletedDateTime);
            $this->addRevision($newRevision);
        }
    }

    public function getDeleted(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getRevisionByDate($referenceDateTime)->getDeleted();
    }

    public function isDeleted(?DateTime $referenceDateTime = null): bool
    {
        return $this->getRevisionByDate($referenceDateTime)->isDeleted();
    }

    public function setEMailDeleteConfirmationDateTime(?DateTime $deletedConfirmationDateTime): void
    {
        if ($this->getEMailDeleteConfirmationDateTime() !== $deletedConfirmationDateTime) {
            $newRevision = clone $this->getRevision();
            $newRevision->setEMailDeleteConfirmationDateTime($deletedConfirmationDateTime);
            $this->addRevision($newRevision);
        }
    }

    public function getEMailDeleteConfirmationDateTime(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getRevisionByDate($referenceDateTime)->getEMailDeleteConfirmationDateTime();
    }
}
