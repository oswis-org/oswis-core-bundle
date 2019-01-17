<?php

namespace Zakjakub\OswisCoreBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

trait EntityUserTrait
{
    use EntityPersonAdvancedTrait;
    use EntityUsernameTrait;
    use EntityEncryptedPasswordTrait;
    use EntityPasswordResetTrait;
    use EntityDeletedTrait;
    use EntityDateRangeTrait;

    /**
     * Salt that was originally used to encode the password.
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $salt;

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    final public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     */
    final public function setSalt(?string $salt): void
    {
        $this->salt = $salt;
    }
}
