<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

\assert(ORM\Annotation::class);

/**
 * Trait adds id field
 */
trait IdTrait
{

    /**
     * Unique (auto-incremented) numeric identifier.
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Get id (unique identifier)
     *
     * @return int
     */
    final public function getId(): int
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
