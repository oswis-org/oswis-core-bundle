<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserRole;
use OswisOrg\OswisCoreBundle\Interfaces\AddressBook\PersonInterface;
use OswisOrg\OswisCoreBundle\Mail\Secret\SecretCarrierInterface;
use OswisOrg\OswisCoreBundle\Traits\User\UserTrait;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Abstract class containing basic properties for user of application.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractAppUser implements UserInterface, EquatableInterface, PersonInterface, SecretCarrierInterface
{
    use UserTrait;

    public function __serialize(): array
    {
        return [
            'id'       => $this->id,
            'username' => $this->username,
            'email'    => $this->email,
            'password' => $this->password,
        ];
    }

    /**
     * @param array{id?: int, username?: string, email?: string, password?: string} $data
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->id = $data['id'] ?? null;
        $this->username = $data['username'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->password = $data['password'] ?? null;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!($user instanceof self)) {
            return false;
        }
        if ($this->getId() !== $user->getId()
            || $this->getUsername() !== $user->getUsername()
            || $this->getEmail() !== $user->getEmail()
            || $this->getPassword() !== $user->getPassword()) {
            return false;
        }

        return true;
    }

    /**
     * Removes sensitive data from the user.
     * This is important if, at any given point, sensitive information like the plain-text password is stored on this
     * object.
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * A generated password is mailed to the person once and stays valid until they change it,
     * so it must never remain in the stored copy of that mail.
     *
     * @return list<string>
     */
    public function getMailSecrets(): array
    {
        $plainPassword = $this->plainPassword;

        return null === $plainPassword || '' === $plainPassword ? [] : [$plainPassword];
    }

    public function hasRole(string $roleName): bool
    {
        return $this->containsRole($roleName);
    }

    public function containsRole(string $roleName): bool
    {
        if (empty($roleName)) {
            return true;
        }
        foreach ($this->getRoles() as $role) {
            if ((is_string($role) && $role === $roleName)
                || ($role instanceof AppUserRole
                    && $role->getRoleString() === $roleName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the roles granted to the user.
     *
     * @return array (Role|string)[] The user roles
     */
    abstract public function getRoles(): array;
}
