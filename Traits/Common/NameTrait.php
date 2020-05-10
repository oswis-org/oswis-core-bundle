<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

/**
 * Trait adds name field and __toString().
 *
 * Trait adds field *name* that contains name or title of entity.
 */
trait NameTrait
{
    /**
     * Name or title of something.
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $name = null;

    /**
     * Sortable variation of name.
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected string $sortableName = '';

    /**
     * Shortened name/shortcut.
     *
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $shortName = null;

    /**
     * Get shortened name.
     */
    public function getShortName(): ?string
    {
        return $this->shortName ?? $this->getName();
    }

    /**
     * Set short name.
     */
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

        return $this->getName();
    }

    public function getSortableName(): string
    {
        return $this->getName() ?? '';
    }

    public function setSortableName(?string $sortableName): string
    {
        return $this->sortableName = $sortableName ?? '';
    }


}
