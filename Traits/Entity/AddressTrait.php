<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Zakjakub\OswisCoreBundle\Entity\Address;

/**
 * Trait adds address fields.
 */
trait AddressTrait
{
    /**
     * First line of street.
     *
     * @var string|null First line of street
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $street = null;

    /**
     * Second line of street.
     *
     * @var string|null Second line of street
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $street2 = null;

    /**
     * City.
     *
     * @var string|null City
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $city = null;

    /**
     * Postal code.
     *
     * @var string|null Postal code
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $postalCode = null;

    /**
     * House number.
     *
     * @var int|null House number
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $houseNumber = null;

    /**
     * Door number.
     *
     * @var int|null Door number
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $doorNumber = null;

    /**
     * Orientation number.
     *
     * @var int|null Door number
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $orientationNumber = null;

    final public function getStreet(): ?string
    {
        return $this->street;
    }

    final public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    final public function getOrientation(): ?int
    {
        return $this->orientationNumber;
    }

    final public function getStreet2(): ?string
    {
        return $this->street2;
    }

    final public function setStreet2(?string $street2): void
    {
        $this->street2 = $street2;
    }

    final public function getCity(): ?string
    {
        return $this->city;
    }

    final public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    final public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    final public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    final public function getHouseNumber(): ?int
    {
        return $this->houseNumber;
    }

    final public function setHouseNumber(?int $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }

    final public function setOrientationNumber(?int $orientationNumber): void
    {
        $this->orientationNumber = $orientationNumber;
    }

    final public function getDoorNumber(): ?int
    {
        return $this->doorNumber;
    }

    final public function setDoorNumber(?int $doorNumber): void
    {
        $this->doorNumber = $doorNumber;
    }

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
