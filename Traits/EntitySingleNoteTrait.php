<?php

namespace Zakjakub\OswisCoreBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds note field
 *
 * Trait adds field *note* that contains some note for entity and allows access to it.
 */
trait EntitySingleNoteTrait
{

    /**
     * Note
     *
     * @var string|null
     *
     * @ORM\Column(nullable=true)
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
