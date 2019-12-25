<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait that adds slug field.
 */
trait SlugTrait
{
    /**
     * Slug.
     * @Doctrine\ORM\Mapping\Column(nullable=true)
     */
    protected ?string $slug = null;

    /**
     * Get slug (or id if not set).
     */
    public function getSlug(): ?string
    {
        return $this->slug ?? ''.$this->getId();
    }

    /**
     * Set slug (or id if not set).
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug ?? ''.$this->getId();
    }
}
