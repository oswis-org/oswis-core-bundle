<?php

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use Serializable;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUserRole;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\UserTrait;

/**
 * Abstract class containing basic properties for user of application.
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractAppUser implements UserInterface, Serializable, EquatableInterface
{
    use BasicEntityTrait;
    use UserTrait;

    /** @see \Serializable::serialize() */
    final public function serialize(): string
    {
        return serialize([$this->id, $this->username, $this->email, $this->password, $this->deleted]);
    }

    /**
     * @param string $serialized
     *
     * @see \Serializable::unserialize()
     */
    final public function unserialize(
        /* @noinspection MissingParameterTypeDeclarationInspection */ $serialized
    ): void {
        [
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->deleted,
        ] = unserialize($serialized, ['allowed_classes' => ['AppUser']]);
    }

    final public function isEqualTo(UserInterface $user): bool
    {
        if (!$user || !($user instanceof self)) {
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
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    final public function eraseCredentials(): void
    {
    }

    /** @noinspection PhpUnused */
    final public function hasRole(string $roleName): bool
    {
        return $this->containsRole($roleName);
    }

    final public function containsRole(string $roleName): bool
    {
        foreach ($this->getRoles() as $role) {
            if ($role instanceof AppUserRole) {
                if ($role->getRoleString() === $roleName) {
                    return true;
                }
            } elseif ($role === $roleName) {
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
