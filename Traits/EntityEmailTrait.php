<?php

namespace Zakjakub\OswisResourcesBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds e-mail field.
 */
trait EntityEmailTrait
{

    /// TODO: Validate e-mail!!!

    /**
     * E-mail address.
     * @var string|null
     * @ORM\Column(name="email",type="string", unique=true, length=60, nullable=true)
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
