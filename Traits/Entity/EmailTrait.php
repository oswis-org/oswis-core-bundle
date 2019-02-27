<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds e-mail field.
 */
trait EmailTrait
{

    /**
     * E-mail address.
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(name="email",type="string", unique=false, length=60, nullable=true)
     * @Symfony\Component\Validator\Constraints\NotBlank()
     * @Symfony\Component\Validator\Constraints\Email(
     *     message = "Zadaná adresa ({{ value }}) není platná.",
     *     mode = "strict"
     * )
     */
    protected $email;

    /**
     * Get e-mail.
     * @return string
     */
    final public function getEmail(): string
    {
        return $this->email ?? '';
    }

    /**
     * Set e-mail.
     *
     * @param null|string $email
     */
    final public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
