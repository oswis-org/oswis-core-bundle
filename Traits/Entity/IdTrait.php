<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds id field.
 */
trait IdTrait
{
    /**
     * Unique (auto-incremented) numeric identifier.
     *
     * @var int|null
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue(strategy="AUTO")
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    protected ?int $id = null;

    /**
     * Get id (unique identifier).
     *
     * @return int
     */
    final public function getId(): ?int
    {
        return $this->id;
    }

    final public function setId(int $id): void
    {
        $this->id = $id;
    }
}
