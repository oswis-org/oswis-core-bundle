<?php /** @noinspection PhpUnused */

/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

/**
 * Trait adds getters and setters for container of entity with nameable fields.
 */
trait NameableBasicContainerTrait
{
    use SlugContainerTrait;

    final public function setName(?string $name): void
    {
        if ($this->getName() !== $name) {
            $newRevision = clone $this->getRevision();
            $newRevision->setName($name);
            $this->addRevision($newRevision);
        }
    }

    final public function getName(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getName();
    }

    final public function setShortName(?string $shortName): void
    {
        if ($this->getShortName() !== $shortName) {
            $newRevision = clone $this->getRevision();
            $newRevision->setShortName($shortName);
            $this->addRevision($newRevision);
        }
    }

    final public function getShortName(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getShortName();
    }

    final public function setDescription(?string $description): void
    {
        if ($this->getDescription() !== $description) {
            $newRevision = clone $this->getRevision();
            $newRevision->setDescription($description);
            $this->addRevision($newRevision);
        }
    }

    final public function getDescription(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getDescription();
    }

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
