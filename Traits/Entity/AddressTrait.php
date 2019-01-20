<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Zakjakub\OswisCoreBundle\Entity\Address;

/**
 * Trait adds address fields
 */
trait AddressTrait
{

    /**
     * First line of street
     *
     * @var string|null $street First line of street
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected $street;

    /**
     * Second line of street
     *
     * @var string|null $street2 Second line of street
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected $street2;

    /**
     * City
     *
     * @var string|null $city City
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected $city;

    /**
     * Postal code
     *
     * @var string|null $postalCode Postal code
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected $postalCode;

    /**
     * House number
     *
     * @var int|null $houseNumber House number
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected $houseNumber;

    /**
     * Door number
     *
     * @var int|null $doorNumber Door number
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected $doorNumber;

    /**
     * Orientation number
     *
     * @var int|null $doorNumber Door number
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected $orientationNumber;

    /**
     * @return string|null
     */
    final public function getStreet(): ?string
    {
        return $this->street ?? '';
    }

    /**
     * @param string|null $street
     */
    final public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    final public function getOrientation(): ?int
    {
        return $this->orientationNumber;
    }

    /**
     * @return string|null
     */
    final public function getStreet2(): ?string
    {
        return $this->street2 ?? '';
    }

    /**
     * @param string|null $street2
     */
    final public function setStreet2(?string $street2): void
    {
        $this->street2 = $street2;
    }

    /**
     * @return string|null
     */
    final public function getCity(): ?string
    {
        return $this->city ?? '';
    }

    /**
     * @param string|null $city
     */
    final public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    final public function getPostalCode(): ?string
    {
        return $this->postalCode ?? '';
    }

    /**
     * @param string|null $postalCode
     */
    final public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return int|null
     */
    final public function getHouseNumber(): ?int
    {
        return $this->houseNumber;
    }

    /**
     * @param int|null $houseNumber
     */
    final public function setHouseNumber(?int $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }

    final public function setOrientationNumber(?int $orientationNumber): void
    {
        $this->orientationNumber = $orientationNumber;
    }

    /**
     * @return int|null
     */
    final public function getDoorNumber(): ?int
    {
        return $this->doorNumber;
    }

    /**
     * @param int|null $doorNumber
     */
    final public function setDoorNumber(?int $doorNumber): void
    {
        $this->doorNumber = $doorNumber;
    }

    /**
     * @return string
     */
    final public function getFullAddress(): string
    {
        $fullAddress = '';
        $fullAddress .= $this->street ?? null;
        $fullAddress .= ($this->doorNumber || $this->houseNumber) ? ' ' : null;
        $fullAddress .= $this->houseNumber ?? null;
        $fullAddress .= ($this->doorNumber && $this->houseNumber) ? '/' : null;
        $fullAddress .= $this->doorNumber ?? null;
        $fullAddress .= $this->street2 ? ', '.$this->street2 : null;
        $fullAddress .= ($this->city || $this->postalCode) ? ',' : null;
        $fullAddress .= $this->city ? ' '.$this->city : null;
        $fullAddress .= $this->postalCode ? ' '.$this->postalCode : null;

        return $fullAddress;
    }

    final public function setFieldsFromAddress(?Address $address = null): void
    {
        if ($address) {
            $this->setStreet($address->street);
            $this->setStreet2($address->street2);
            $this->setHouseNumber($address->houseNumber);
            $this->setDoorNumber($address->doorNumber);
            $this->setCity($address->city);
            $this->setPostalCode($address->postalCode);
        }
    }


}
