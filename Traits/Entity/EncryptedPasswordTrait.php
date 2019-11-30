<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds encrypted password field.
 */
trait EncryptedPasswordTrait
{
    /**
     * Encrypted password.
     *
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(name="password", type="string", length=255, nullable=true)
     */
    protected ?string $password = null;

    /**
     * Get encrypted password.
     */
    final public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set encrypted password.
     */
    final public function setPassword(?string $password): void
    {
        $this->password = empty($password) ? null : $password;
    }
}
