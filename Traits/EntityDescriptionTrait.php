<?php

namespace Zakjakub\OswisCoreBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds description field
 */
trait EntityDescriptionTrait
{

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    protected $description;


    /**
     * Get description
     *
     * @return string
     */
    final public function getDescription(): string
    {
        return $this->description ?? '';
    }

    /**
     * Set description
     *
     * @param null|string $description
     */
    final public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
