<?php

namespace Zakjakub\OswisCoreBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds encrypted password field.
 */
trait EncryptedPasswordTrait
{

    /**
     * Encrypted password.
     * @var string|null
     * @ORM\Column(name="password", type="string", length=64, nullable=true)
     */
    protected $password;

    /**
     * Get encrypted password.
     * @return null|string
     */
    final public function getPassword(): string
    {
        return $this->password ?? '';
    }

    /**
     * Set encrypted password.
     *
     * @param null|string $password
     */
    final public function setPassword(?string $password): void
    {
        $this->password = $password ?? '';
    }
}
