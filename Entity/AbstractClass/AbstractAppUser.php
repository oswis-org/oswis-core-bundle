<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserRole;
use OswisOrg\OswisCoreBundle\Interfaces\AddressBook\PersonInterface;
use OswisOrg\OswisCoreBundle\Traits\User\UserTrait;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Abstract class containing basic properties for user of application.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractAppUser implements UserInterface, EquatableInterface, PersonInterface
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

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->password = $data['password'];
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
