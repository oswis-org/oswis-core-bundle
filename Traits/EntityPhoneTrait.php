<?php

namespace Zakjakub\OswisCoreBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds phone number field.
 */
trait EntityPhoneTrait
{

    /**
     * Phone number.
     * @var string|null
     * @ORM\Column(type="string", unique=true, length=60, nullable=true)
     */
    protected $phone;

    /**
     * Get phone number.
     * @return string
     */
    final public function getPhone(): string
    {
        return $this->phone ?? '';
    }

    /**
     * Set phone number.
     *
     * @param null|string $phone
     */
    final public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }
}
