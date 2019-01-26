<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds id field
 */
trait IdTrait
{

    /**
     * Unique (auto-incremented) numeric identifier.
     * @var int
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue(strategy="AUTO")
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    protected $id;

    /**
     * Get id (unique identifier)
     *
     * @return int
     */
    final public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    final public function setId(int $id): void
    {
        $this->id = $id;
    }
}
