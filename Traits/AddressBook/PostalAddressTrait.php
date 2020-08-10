<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\AddressBook;

use OswisOrg\OswisCoreBundle\Entity\NonPersistent\PostalAddress;

/**
 * Trait adds fields for postal address.
 */
trait PostalAddressTrait
{
    /**
     * @var string|null First line of street
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $street = null;

    /**
     * @var string|null Second line of street
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $street2 = null;

    /**
     * @var string|null City
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $city = null;

    /**
     * @var string|null Postal code
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $postalCode = null;

    /**
     * @var int|null House number
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?int $houseNumber = null;

    /**
     * @var int|null Door number
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?int $doorNumber = null;

    /**
     * @var int|null Door number
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
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
        $fullAddress = $this->getFirstStreetLine();
        $fullAddress .= $this->street2 ? ', '.$this->street2 : null;
        $fullAddress .= ($this->city || $this->postalCode) ? ',' : null;
        $fullAddress .= $this->city ? ' '.$this->city : null;
        $fullAddress .= $this->postalCode ? ' '.$this->postalCode : null;

        return $fullAddress;
    }

    public function getFirstStreetLine(): string
    {
        $firstStreetLine = '';
        $firstStreetLine .= $this->street ?? null;
        $firstStreetLine .= ($this->doorNumber || $this->houseNumber) ? ' ' : null;
        $firstStreetLine .= $this->houseNumber ?? null;
        $firstStreetLine .= ($this->doorNumber && $this->houseNumber) ? '/' : null;
        $firstStreetLine .= $this->doorNumber ?? null;

        return $firstStreetLine;
    }

    public function setFieldsFromAddress(?PostalAddress $address = null): void
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
