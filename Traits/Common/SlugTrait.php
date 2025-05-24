<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;
use OswisOrg\OswisCoreBundle\Utils\StringUtils;

/**
 * Trait that adds slug field.
 */
trait SlugTrait
{
    /** Slug (slug is auto-generated if forcedSlug is not set). */
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[Column(type: 'string', nullable: true)]
    protected ?string $slug = null;

    /** Forced slug - set by user, not auto-generated. */
    #[Column(type: 'string', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    protected ?string $forcedSlug = null;

    public function getForcedSlug(): ?string
    {
        return !empty($this->forcedSlug) ? $this->forcedSlug : null;
    }

    public function setForcedSlug(?string $forcedSlug): void
    {
        $this->forcedSlug = StringUtils::hyphenize($forcedSlug);
        /** @phpstan-ignore-next-line */
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
