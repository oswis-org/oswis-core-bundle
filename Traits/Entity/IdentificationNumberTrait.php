<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds company identification number.
 */
trait IdentificationNumberTrait
{

    /**
     * Identification number.
     *
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     * @Symfony\Component\Validator\Constraints\Length(
     *      min = 6,
     *      max = 10,
     *      minMessage = "IČ {{ value }} je příliš krátké, musí obsahovat nejméně {{ limit }} znaků.",
     *      maxMessage = "IČ {{ value }} je příliš dlouhé, musí obsahovat nejvíce {{ limit }} znaků.",
     * )
     */
    protected $identificationNumber;

    /**
     * @return string
     */
    final public function getIdentificationNumber(): ?string
    {
        return $this->identificationNumber;
    }

    /**
     * @param null|string $identificationNumber
     */
    final public function setIdentificationNumber(?string $identificationNumber): void
    {
        $this->identificationNumber = $identificationNumber;
    }
}
