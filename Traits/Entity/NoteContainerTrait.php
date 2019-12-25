<?php /** @noinspection MethodShouldBeFinalInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait NoteContainerTrait
{
    public function setNote(?string $note): void
    {
        if ($this->getNote() !== $note) {
            $newRevision = clone $this->getRevision();
            $newRevision->setNote($note);
            $this->addRevision($newRevision);
        }
    }

    public function getNote(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getNote();
    }
}
