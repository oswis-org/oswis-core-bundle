<?php /** @noinspection MethodShouldBeFinalInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait InternalNoteContainerTrait
{
    public function setInternalNote(?string $note): void
    {
        if ($this->getInternalNote() !== $note) {
            $newRevision = clone $this->getRevision();
            $newRevision->setInternalNote($note);
            $this->addRevision($newRevision);
        }
    }

    public function getInternalNote(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getInternalNote();
    }
}
