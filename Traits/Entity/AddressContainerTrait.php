<?php
/** @noinspection PhpDocRedundantThrowsInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Zakjakub\OswisCoreBundle\Exceptions\RevisionMissingException;

/**
 * Trait adds getters and setters for container of entity with address fields.
 */
trait AddressContainerTrait
{

    /**
     * @param DateTime|null $dateTime
     *
     * @return string|null
     * @throws RevisionMissingException
     */
    final public function getCity(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getCity();
    }

    /**
     * @param DateTime|null $dateTime
     *
     * @return int|null
     * @throws RevisionMissingException
     */
    final public function getDoorNumber(?DateTime $dateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getDoorNumber();
    }

    /**
     * @param string|null $street
     *
     * @throws RevisionMissingException
     */
    final public function setStreet(?string $street): void
    {
        if ($this->getStreet() !== $street) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setStreet($street);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @param DateTime|null $dateTime
     *
     * @return string|null
     * @throws RevisionMissingException
     */
    final public function getStreet(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getStreet();
    }

    /**
     * @param string|null $street2
     *
     * @throws RevisionMissingException
     */
    final public function setStreet2(?string $street2): void
    {
        if ($this->getStreet2() !== $street2) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setStreet2($street2);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @param DateTime|null $dateTime
     *
     * @return string|null
     * @throws RevisionMissingException
     */
    final public function getStreet2(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getStreet2();
    }

    /**
     * @param int|null $houseNumber
     *
     * @throws RevisionMissingException
     */
    final public function setHouseNumber(?int $houseNumber): void
    {
        if ($this->getHouseNumber() !== $houseNumber) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setHouseNumber($houseNumber);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @param DateTime|null $dateTime
     *
     * @return int|null
     * @throws RevisionMissingException
     */
    final public function getHouseNumber(?DateTime $dateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getHouseNumber();
    }

    /**
     * @param int|null $doorNumber
     *
     * @throws RevisionMissingException
     */
    final public function setDoorNumber(?int $doorNumber): void
    {
        if ($this->getStreet() !== $doorNumber) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setStreet($doorNumber);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @param string|null $postalCode
     *
     * @throws RevisionMissingException
     */
    final public function setPostalCode(?string $postalCode): void
    {
        if ($this->getPostalCode() !== $postalCode) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setPostalCode($postalCode);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @param DateTime|null $dateTime
     *
     * @return string|null
     * @throws RevisionMissingException
     */
    final public function getPostalCode(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getPostalCode();
    }

    /**
     * @param string|null $city
     *
     * @throws RevisionMissingException
     */
    final public function setCity(?string $city): void
    {
        if ($this->getStreet() !== $city) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setStreet($city);
            $this->addRevision($newRevision);
        }
    }
}
