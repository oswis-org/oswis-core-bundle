<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

/**
 * Trait adds internalNote field.
 *
 * Trait adds field *note* that contains some note for entity + getter and setter.
 */
trait InternalNoteTrait
{
    /**
     * Internal (non-public) note.
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $internalNote = null;

    /**
     * Get internal note.
     */
    public function getInternalNote(): ?string
    {
        return $this->internalNote;
    }

    /**
     * Set internal note.
     */
    public function setInternalNote(?string $internalNote): void
    {
        $this->internalNote = $internalNote;
    }
}
