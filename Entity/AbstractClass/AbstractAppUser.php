<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use Serializable;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUserRole;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\UserTrait;

/**
 * Abstract class containing basic properties for user of application.
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractAppUser implements UserInterface, Serializable, EquatableInterface
{
    use BasicEntityTrait;
    use UserTrait;

    public function serialize(): string
    {
        return serialize([$this->id, $this->username, $this->email, $this->password, $this->deleted]);
    }

    public function unserialize(/* @noinspection MissingParameterTypeDeclarationInspection */ $serialized): void
    {
        [$this->id, $this->username, $this->email, $this->password, $this->deleted] = unserialize($serialized, ['allowed_classes' => ['AppUser']]);
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!($user instanceof self)) {
            return false;
        }
        if ($this->id !== $user->getId() || $this->username !== $user->getUsername()) {
            return false;
        }
        if ($this->email !== $user->getEmail() || $this->password !== $user->getPassword()) {
            return false;
        }

        return true;
    }

    /**
     * Removes sensitive data from the user.
     * This is important if, at any given point, sensitive information like the plain-text password is stored on this object.
     */
    public function eraseCredentials(): void
    {
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
