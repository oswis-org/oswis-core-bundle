<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds foreignNationality boolean field
 */
trait ForeignNationalityTrait
{

    /**
     * Foreign nationality.
     * @var boolean
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    protected $foreignNationality;

    /**
     * @return boolean
     */
    final public function getForeignNationality(): ?bool
    {
        return $this->foreignNationality ?? false;
    }

    /**
     * @param boolean $foreignNationality
     */
    final public function setForeignNationality(?bool $foreignNationality): void
    {
        $this->foreignNationality = $foreignNationality;
    }
}
