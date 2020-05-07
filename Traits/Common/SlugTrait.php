<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use OswisOrg\OswisCoreBundle\Utils\StringUtils;

/**
 * Trait that adds slug field.
 */
trait SlugTrait
{
    /**
     * Slug (auto-generated if forcedSlug not set).
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $slug = null;

    /**
     * Forced slug - set by user, not auto-generated.
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $forcedSlug = null;

    public function getForcedSlug(): ?string
    {
        return !empty($this->forcedSlug) ? $this->forcedSlug : null;
    }

    public function setForcedSlug(?string $forcedSlug): void
    {
        $this->forcedSlug = StringUtils::hyphenize($forcedSlug);
        method_exists($this, 'updateSlug') ? $this->updateSlug() : $this->setSlug($this->getSlug());
    }

    public function getSlug(): string
    {
        return $this->getForcedSlug() ?? $this->slug ?? ''.$this->getId();
    }

    public function setSlug(?string $slug): string
    {
        return $this->slug = $this->getForcedSlug() ?? (!empty($slug) ? $slug : ''.$this->getId());
    }
}
