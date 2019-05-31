<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds username field.
 */
trait UsernameTrait
{

    /**
     * Username.
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(name="username", type="string", length=50, unique=true, nullable=true)
     */
    protected $username;

    /**
     * Get username.
     * @return null|string
     */
    final public function getUsername(): ?string
    {
        return $this->username ?? null;
    }

    /**
     * Set username.
     *
     * @param null|string $username
     */
    final public function setUsername(?string $username): void
    {
        $this->username = $username ?? null;
    }
}
