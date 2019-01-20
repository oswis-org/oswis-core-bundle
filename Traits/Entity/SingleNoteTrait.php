<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds note field
 *
 * Trait adds field *note* that contains some note for entity and allows access to it.
 */
trait SingleNoteTrait
{

    /**
     * Note
     *
     * @var string|null
     *
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     */
    protected $note;

    /**
     * Get note
     *
     * @return null|string
     */
    final public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * Set note
     *
     * @param null|string $note
     */
    final public function setNote(?string $note): void
    {
        $this->note = $note;
    }
}
