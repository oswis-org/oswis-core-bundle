<?php

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\DeletedTrait;

/**
 * Abstract bundle user (for creating specific user extension in bundle).
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractBundleUser
{
    use BasicEntityTrait;
    use DeletedTrait;

    /**
     * Linked AppUser from core bundle.
     * @var AppUser|null
     * @Doctrine\ORM\Mapping\OneToOne(
     *     targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUser",
     *     cascade={"all"},
     *     fetch="EAGER"
     * )
     * @Doctrine\ORM\Mapping\JoinColumn(name="app_user_id", referencedColumnName="id")
     */
    private $appUser;

    /**
     * AbstractBundleUser constructor.
     *
     * @param AppUser|null $appUser
     */
    public function __construct(?AppUser $appUser = null)
    {
        $this->setAppUser($appUser);
    }

    /**
     * @return string
     */
    final public function getUsername(): string
    {
        return $this->getAppUser() ? $this->getAppUser()->getUsername() : null;
    }

    /**
     * @return AppUser|null
     */
    final public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    /**
     * @param AppUser|null $appUser
     */
    final public function setAppUser(?AppUser $appUser): void
    {
        $this->appUser = $appUser;
    }

    /**
     * @return string
     */
    final public function getFullName(): string
    {
        return $this->getAppUser() ? ($this->getAppUser()->getFullName() ?? $this->getAppUser()->getUsername()) : null;
    }

    /**
     * @return string
     */
    final public function getFullAddress(): string
    {
        return $this->getAppUser() ? $this->getAppUser()->getFullAddress() : null;
    }

    /**
     * @return string
     */
    final public function getEmail(): string
    {
        return $this->getAppUser() ? $this->getAppUser()->getEmail() : null;
    }

    /**
     * @return string
     */
    final public function getPhone(): string
    {
        return $this->getAppUser() ? $this->getAppUser()->getPhone() : null;
    }
}
