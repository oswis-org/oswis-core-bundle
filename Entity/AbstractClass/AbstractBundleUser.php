<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Interfaces\BasicEntityInterface;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\DeletedTrait;

/**
 * Abstract bundle user (for creating specific user extension in bundle).
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractBundleUser implements BasicEntityInterface
{
    use BasicEntityTrait;
    use DeletedTrait;

    /**
     * Linked AppUser from core bundle.
     *
     * @var AppUser|null
     * @Doctrine\ORM\Mapping\OneToOne(
     *     targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUser",
     *     cascade={"all"},
     *     fetch="EAGER"
     * )
     * @Doctrine\ORM\Mapping\JoinColumn(name="app_user_id", referencedColumnName="id")
     */
    private ?AppUser $appUser = null;

    /**
     * AbstractBundleUser constructor.
     */
    public function __construct(?AppUser $appUser = null)
    {
        $this->setAppUser($appUser);
    }

    final public function getUsername(): string
    {
        return $this->getAppUser() ? $this->getAppUser()->getUsername() : '';
    }

    final public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    final public function setAppUser(?AppUser $appUser): void
    {
        $this->appUser = $appUser;
    }

    final public function getFullName(): string
    {
        return $this->getAppUser() ? ($this->getAppUser()->getFullName() ?? $this->getAppUser()->getUsername()) : '';
    }

    final public function getFullAddress(): string
    {
        return $this->getAppUser() ? $this->getAppUser()->getFullAddress() : '';
    }

    final public function getEmail(): string
    {
        return $this->getAppUser() ? $this->getAppUser()->getEmail() : '';
    }

    final public function getPhone(): string
    {
        return $this->getAppUser() ? $this->getAppUser()->getPhone() : '';
    }
}
