<?php

namespace Zakjakub\OswisCoreBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds active field
 */
trait ActiveTrait
{

    /**
     * Active
     *
     * @var bool|null
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $active;

    /**
     * Get active
     *
     * @return bool
     */
    final public function getActive(): bool
    {
        return $this->active ?? false;
    }

    /**
     * Set active
     *
     * @param bool $active
     */
    final public function setActive(?bool $active = false): void
    {
        $this->active = $active ?? false;
    }
}
