<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserRole;
use OswisOrg\OswisCoreBundle\Interfaces\AddressBook\PersonInterface;
use OswisOrg\OswisCoreBundle\Traits\User\UserTrait;
use Serializable;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Abstract class containing basic properties for user of application.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractAppUser implements UserInterface, Serializable, EquatableInterface, PersonInterface
{
    use UserTrait;

    // TODO: Refactor: Wrap slug as username.
    public function serialize(): string
    {
        return serialize([$this->id, $this->username, $this->email, $this->password]);
    }

    /**
     * {@inheritDoc}
     * @param mixed $serialized
     *
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    public function unserialize($serialized): void
    {
        [$this->id, $this->username, $this->email, $this->password] = unserialize($serialized, ['allowed_classes' => ['AppUser']]);
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!($user instanceof self)) {
            return false;
        }
        if ($this->getId() !== $user->getId() || $this->getUsername() !== $user->getUsername()) {
            return false;
        }
        if ($this->getEmail() !== $user->getEmail() || $this->getPassword() !== $user->getPassword()) {
            return false;
        }
        // if ($this->isActivated() !== $user->isActivated() || $this->isDeleted() !== $user->isDeleted()) {
        //     return false;
        // }
        return true;
    }

    /**
     * Removes sensitive data from the user.
     * This is important if, at any given point, sensitive information like the plain-text password is stored on this object.
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
        foreach ($this->getRoles() as $role) {
            if ((is_string($role) && $role === $roleName) || ($role instanceof AppUserRole && $role->getRoleString() === $roleName)) {
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
