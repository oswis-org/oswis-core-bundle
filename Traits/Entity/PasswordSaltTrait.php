<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Entity;

trait PasswordSaltTrait
{
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
