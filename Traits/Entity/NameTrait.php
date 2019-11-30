<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds name field and __toString().
 *
 * Trait adds field *name* that contains name or title of entity.
 */
trait NameTrait
{
    /**
     * Name/title.
     *
     * @var string|null
     *
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $name = null;

    /**
     * Short name/shortcut.
     *
     * @var string|null
     *
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $shortName = null;

    /**
     * Get short name.
     */
    final public function getShortName(): ?string
    {
        return $this->shortName ?? $this->getName();
    }

    /**
     * Set short name.
     */
    final public function setShortName(?string $shortName): void
    {
        $this->shortName = $shortName;
    }

    /**
     * Get name/title.
     */
    final public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name/title.
     */
    final public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
