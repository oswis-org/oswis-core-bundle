<?php
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait NoteContainerTrait
{

    /**
     * @param string|null $note
     *
     * @throws RevisionMissingException
     */
    final public function setNote(?string $note): void
    {
        if ($this->getNote() !== $note) {
            $newRevision = clone $this->getRevision();
            $newRevision->setNote($note);
            $this->addRevision($newRevision);
        }
    }

    final public function getNote(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getNote();
    }
}
