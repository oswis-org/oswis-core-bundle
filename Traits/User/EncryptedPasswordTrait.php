<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\User;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Trait adds encrypted password field.
 */
trait EncryptedPasswordTrait
{
    /**
     * @var string|null Temporary storage for plain text password (used in e-mails), NOT PERSISTED!
     */
    public ?string $plainPassword = null;

    /**
     * Encrypted password.
     * @Doctrine\ORM\Mapping\Column(name="password", type="string", length=255, nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     */
    protected ?string $password = null;

    /**
     * Salt that was originally used to encode the password.
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $salt;

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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword, ?UserPasswordEncoderInterface $encoder = null, bool $deletePlain = true): void
    {
        $this->plainPassword = $deletePlain ? null : $plainPassword;
        if (null !== $encoder) {
            $this->encryptPassword($plainPassword, $encoder);
        }
    }

    abstract public function encryptPassword(?string $plainPassword, UserPasswordEncoderInterface $encoder): void;
}
