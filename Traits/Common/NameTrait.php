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

/**
 * Trait adds name field and __toString().
 *
 * Trait adds field *name* that contains name or title of entity.
 */
trait NameTrait
{
    /** Name or title of something. */
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[Column(type: 'string', nullable: true)]
    protected ?string $name = null;

    /** Sortable variation of name. */
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[Column(type: 'string', nullable: true)]
    protected ?string $sortableName = '';

    /** Shortened name/shortcut. */
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[Column(type: 'string', nullable: true)]
    protected ?string $shortName = null;

    /** Get shortened name. */
    public function getShortName(): ?string
    {
        return $this->shortName ?? $this->getName();
    }

    /** Set short name. */
    public function setShortName(?string $shortName): void
    {
        $this->shortName = $shortName;
    }

    public function getName(): ?string
    {
        return $this->updateName();
    }

    public function setName(?string $name): ?string
    {
        $this->name = $name;

        return $this->updateName();
    }

    public function updateName(): ?string
    {
        $this->setSortableName($this->getSortableName());

        return $this->name;
    }

    public function getSortableName(): string
    {
        return $this->name ?? '';
    }

    public function setSortableName(?string $sortableName): string
    {
        return $this->sortableName = $sortableName ?? '';
    }
}
