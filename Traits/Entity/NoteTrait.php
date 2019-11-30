<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds note field.
 *
 * Trait adds field *note* that contains some note for entity and allows access to it.
 */
trait NoteTrait
{
    /**
     * Note.
     *
     * @var string|null
     *
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=true)
     */
    protected ?string $note = null;

    /**
     * Get note.
     */
    final public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * Set note.
     */
    final public function setNote(?string $note): void
    {
        $this->note = $note;
    }
}
