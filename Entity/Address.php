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

    /**
     * @var string|null
     */
    public ?string $street;

    /**
     * @var string|null
     */
    public ?string $street2;

    /**
     * @var string|null
     */
    public ?string $city;

    /**
     * @var string|null
     */
    public ?string $postalCode;

    /**
     * @var int|null
     */
    public ?int $houseNumber;

    /**
     * @var int|null
     */
    public ?int $doorNumber;

    /**
     * @var int|null
     */
    public ?int $orientationNumber;

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
