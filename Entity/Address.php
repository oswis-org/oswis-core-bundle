<?php

namespace Zakjakub\OswisCoreBundle\Entity;

/**
 * Class Address
 * @package OswisResources
 */
class Address
{

    /**
     * @var string|null
     */
    public $street;

    /**
     * @var string|null
     */
    public $street2;

    /**
     * @var string|null
     */
    public $city;

    /**
     * @var string|null
     */
    public $postalCode;

    /**
     * @var int|null
     */
    public $houseNumber;

    /**
     * @var int|null
     */
    public $doorNumber;

    /**
     * @var int|null
     */
    public $orientationNumber;

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