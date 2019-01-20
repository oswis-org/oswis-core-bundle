<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds phone number field.
 */
trait PhoneTrait
{

    /**
     * Phone number.
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", unique=true, length=60, nullable=true)
     * @Symfony\Component\Validator\Constraints\Regex(
     *     pattern="/^(\+420|\+421)? ?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}$/",
     *     message="Zadané číslo ({{ value }}) není platným českým nebo slovenským telefonním číslem."
     * )
     * @Symfony\Component\Validator\Constraints\Length(
     *      min = 9,
     *      max = 15
     * )
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
