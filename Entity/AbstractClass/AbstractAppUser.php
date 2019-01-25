<?php

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Zakjakub\OswisCoreBundle\Traits\Entity\UserTrait;

// Dummy statement -> use not deleted as unused.
\assert(Timestampable::class);

abstract class AbstractAppUser implements UserInterface, \Serializable, EquatableInterface
{
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
     * @see \Serializable::unserialize()
     *
     * @param string $serialized
     *
     * @return void
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
