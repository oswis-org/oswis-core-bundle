<?php

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use Doctrine\Common\Collections\ArrayCollection;
use Serializable;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\UserTrait;

abstract class AbstractAppUser implements UserInterface, Serializable, EquatableInterface
{
    use BasicEntityTrait;
    use UserTrait;

    /** @see \Serializable::serialize() */
    final public function serialize(): string
    {
        return serialize(
            array(
                $this->id,
                $this->username,
                $this->email,
                $this->password,
                $this->deleted,
            )
        );
    }

    /**
     * @param string $serialized
     *
     * @return void
     * @see \Serializable::unserialize()
     *
     */
    final public function unserialize(
        /** @noinspection MissingParameterTypeDeclarationInspection */
        $serialized
    ): void {
        [
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->deleted,
        ] = unserialize($serialized, array('allowed_classes' => ['AppUser']));
    }

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    final public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->id !== $user->getId()) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        if ($this->email !== $user->getEmail()) {
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

    final public function hasRole(string $roleName): bool
    {
        return $this->containsRole($roleName);
    }

    final public function containsRole(string $roleName): bool
    {
        $roles = new ArrayCollection($this->getRoles());

        return $roles->contains($roleName);
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return array('ROLE_USER');
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array (Role|string)[] The user roles
     */
    abstract public function getRoles(): array;
}
