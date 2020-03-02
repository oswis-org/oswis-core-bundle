<?php /** @noinspection MethodShouldBeFinalInspection */

/**
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Zakjakub\OswisCoreBundle\Entity\Address;

/**
 * Trait adds fields for postal address.
 */
trait AddressTrait
{
    /**
     * @var string|null First line of street
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $street = null;

    /**
     * @var string|null Second line of street
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $street2 = null;

    /**
     * @var string|null City
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $city = null;

    /**
     * @var string|null Postal code
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $postalCode = null;

    /**
     * @var int|null House number
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $houseNumber = null;

    /**
     * @var int|null Door number
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $doorNumber = null;

    /**
     * @var int|null Door number
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $orientationNumber = null;

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    public function getOrientation(): ?int
    {
        return $this->getOrientationNumber();
    }

    public function getOrientationNumber(): ?int
    {
        return $this->orientationNumber;
    }

    public function setOrientationNumber(?int $orientationNumber): void
    {
        $this->orientationNumber = $orientationNumber;
    }

    public function getStreet2(): ?string
    {
        return $this->street2;
    }

    public function setStreet2(?string $street2): void
    {
        $this->street2 = $street2;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getHouseNumber(): ?int
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(?int $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }

    public function getDoorNumber(): ?int
    {
        return $this->doorNumber;
    }

    public function setDoorNumber(?int $doorNumber): void
    {
        $this->doorNumber = $doorNumber;
    }

    public function getFullAddress(): string
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

    public function setFieldsFromAddress(?Address $address = null): void
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
