<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds internalNote field.
 *
 * Trait adds field *note* that contains some note for entity and allows access to it.
 */
trait InternalNoteTrait
{

    /**
     * Internal note.
     *
     * @var string|null
     *
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     */
    protected ?string $internalNote;

    /**
     * Get internal note.
     *
     * @return null|string
     */
    final public function getInternalNote(): ?string
    {
        return $this->internalNote;
    }

    /**
     * Set internal note.
     *
     * @param null|string $internalNote
     */
    final public function setInternalNote(?string $internalNote): void
    {
        $this->internalNote = $internalNote;
    }
}
