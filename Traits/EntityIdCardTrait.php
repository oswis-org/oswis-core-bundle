<?php

namespace Zakjakub\OswisResourcesBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds description field
 */
trait EntityIdCardTrait
{

    /**
     * ID card type (as string)
     * @var string
     * @ORM\Column(type="string")
     */
    protected $idCardType;

    /**
     * ID card number (as string).
     * @var string
     * @ORM\Column(type="string")
     */
    protected $idCardNumber;

    /**
     * @return string
     */
    final public function getIdCardType(): ?string
    {
        return $this->idCardType;
    }

    /**
     * @param string $idCardType
     */
    final public function setIdCardType(?string $idCardType): void
    {
        $this->idCardType = $idCardType;
    }

    /**
     * @return string
     */
    final public function getIdCardNumber(): ?string
    {
        return $this->idCardNumber;
    }

    /**
     * @param string $idCardNumber
     */
    final public function setIdCardNumber(?string $idCardNumber): void
    {
        $this->idCardNumber = $idCardNumber;
    }
}
