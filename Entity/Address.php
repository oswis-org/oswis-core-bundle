<?php

namespace Zakjakub\OswisCoreBundle\Entity;

/** @noinspection ClassNameCollisionInspection */

/**
 * Postal address.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
class Address
{
    public ?string $street = null;

    public ?string $street2 = null;

    public ?string $city = null;

    public ?string $postalCode = null;

    public ?int $houseNumber = null;

    public ?int $doorNumber = null;

    public ?int $orientationNumber = null;

    public function __construct(
        ?string $street = null,
        ?string $street2 = null,
        ?string $city = null,
        ?string $postalCode = null,
        ?int $houseNumber = null,
        ?int $orientationNumber = null,
        ?int $doorNumber = null
    ) {
        $this->street = $street;
        $this->street2 = $street2;
        $this->city = $city;
        $this->postalCode = $postalCode;
        $this->houseNumber = $houseNumber;
        $this->orientationNumber = $orientationNumber;
        $this->doorNumber = $doorNumber;
    }
}
