<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUndefinedMethodInspection
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

/**
 * Trait adds getters and setters for container of entity with address fields.
 */
trait AddressContainerTrait
{
    public function getCity(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getCity();
    }

    public function getDoorNumber(?DateTime $dateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getDoorNumber();
    }

    public function setStreet(?string $street): void
    {
        if ($this->getStreet() !== $street) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setStreet($street);
            $this->addRevision($newRevision);
        }
    }

    public function getStreet(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getStreet();
    }

    public function setStreet2(?string $street2): void
    {
        if ($this->getStreet2() !== $street2) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setStreet2($street2);
            $this->addRevision($newRevision);
        }
    }

    public function getStreet2(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getStreet2();
    }

    public function setHouseNumber(?int $houseNumber): void
    {
        if ($this->getHouseNumber() !== $houseNumber) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setHouseNumber($houseNumber);
            $this->addRevision($newRevision);
        }
    }

    public function getHouseNumber(?DateTime $dateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getHouseNumber();
    }

    public function setDoorNumber(?int $doorNumber): void
    {
        if ($this->getStreet() !== $doorNumber) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setStreet($doorNumber);
            $this->addRevision($newRevision);
        }
    }

    public function setPostalCode(?string $postalCode): void
    {
        if ($this->getPostalCode() !== $postalCode) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setPostalCode($postalCode);
            $this->addRevision($newRevision);
        }
    }

    public function getPostalCode(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getPostalCode();
    }

    public function setCity(?string $city): void
    {
        if ($this->getStreet() !== $city) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setStreet($city);
            $this->addRevision($newRevision);
        }
    }
}
