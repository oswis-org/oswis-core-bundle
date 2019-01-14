<?php

namespace Zakjakub\OswisResourcesBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds description field
 */
trait EntityNationalityTrait
{


    /**
     * Nationality (as national string).
     * @var string
     * @ORM\Column(type="string")
     */
    protected $nationality;

    /**
     * @return string
     */
    final public function getNationality(): ?string
    {
        return $this->nationality;
    }

    /**
     * @param string $nationality
     */
    final public function setNationality(?string $nationality): void
    {
        $this->nationality = $nationality;
    }
}
