<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Entity;

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
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $name = null;

    /**
     * Shortened name/shortcut.
     *
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $shortName = null;

    /**
     * Get short name.
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

    /**
     * Get name/title.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name/title.
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
