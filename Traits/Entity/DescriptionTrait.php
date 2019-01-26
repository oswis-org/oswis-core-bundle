<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds description field
 */
trait DescriptionTrait
{

    /**
     * Description
     *
     * @var string|null
     *
     * @Doctrine\ORM\Mapping\Column(nullable=true)
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