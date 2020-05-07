<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

/**
 * Trait adds note field.
 *
 * Trait adds field *note* that contains some note for entity and allows access to it.
 */
trait NoteTrait
{
    /**
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=true)
     */
    protected ?string $note = null;

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): void
    {
        $this->note = $note;
    }
}
