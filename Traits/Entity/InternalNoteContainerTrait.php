<?php
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait InternalNoteContainerTrait
{

    /**
     * @param string|null $note
     *
     * @throws RevisionMissingException
     */
    final public function setInternalNote(?string $note): void
    {
        if ($this->getInternalNote() !== $note) {
            $newRevision = clone $this->getRevision();
            $newRevision->setInternalNote($note);
            $this->addRevision($newRevision);
        }
    }

    final public function getInternalNote(?\DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getInternalNote();
    }

}
