<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\User;

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
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set encrypted password.
     */
    public function setPassword(?string $password): void
    {
        $this->password = empty($password) ? null : $password;
    }

    /**
     * Salt that was originally used to encode the password.
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $salt;

    /**
     * Returns the salt that was originally used to encode the password.
     * This can return null if the password was not encoded using a salt.
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(?string $salt): void
    {
        $this->salt = $salt;
    }
}
