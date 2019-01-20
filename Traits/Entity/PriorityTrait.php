<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds priority field
 */
trait PriorityTrait
{

    /**
     * Priority (numeric)
     *
     * @var int|null
     *
     * @ORM\Column(nullable=false, options={"default": 0})
     */
    private $priority;

    /**
     * Get priority
     *
     * @return int
     */
    final public function getPriority(): int
    {
        return $this->priority ?? 0;
    }

    /**
     * Set priority
     *
     * @param int $priority
     */
    final public function setPriority(int $priority): void
    {
        $this->priority = $priority ?? 0;
    }
}
