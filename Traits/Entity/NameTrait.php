<?php

namespace Zakjakub\OswisCoreBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds name field and __toString()
 *
 * Trait adds field *name* that contains name or title of entity.
 */
trait NameTrait
{

    /**
     * Name/title
     *
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * Short name/shortcut.
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $shortName;

    /**
     * Get short name.
     *
     * @return null|string
     */
    final public function getShortName(): ?string
    {
        return $this->shortName ?? $this->getName();
    }

    /**
     * Set short name.
     *
     * @param null|string $shortName
     */
    final public function setShortName(?string $shortName): void
    {
        $this->shortName = $shortName;
    }

    /**
     * Get name/title
     *
     * @return null|string
     */
    final public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name/title
     *
     * @param null|string $name
     */
    final public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
