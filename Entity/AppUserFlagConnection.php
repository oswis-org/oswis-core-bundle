<?php

namespace Zakjakub\OswisCoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Zakjakub\OswisCoreBundle\Filter\SearchAnnotation as Searchable;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;

/**
 * Connection between app user and used app user flag.
 * @Doctrine\ORM\Mapping\Entity()
 * @Doctrine\ORM\Mapping\Table(name="core_app_user_flag_connection")
 * @ApiFilter(OrderFilter::class)
 * @Searchable({
 *     "id",
 *     "name",
 *     "description",
 *     "note"
 * })
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
class AppUserFlagConnection
{
    use BasicEntityTrait;

    /**
     * Flag used by app user.
     * @var AppUserFlag|null
     * @Doctrine\ORM\Mapping\ManyToOne(
     *     targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUserFlag",
     *     inversedBy="appUserFlagConnections",
     *     fetch="EAGER"
     * )
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=true)
     */
    protected $appUserFlag;

    /**
     * App user.
     * @var AppUser|null
     * @Doctrine\ORM\Mapping\ManyToOne(
     *     targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUser",
     *     inversedBy="appUserFlags",
     *     fetch="EAGER"
     * )
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=true)
     */
    protected $appUser;

    /**
     * Get app user.
     * @return AppUser|null
     */
    final public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    /**
     * Set app user.
     * @param AppUser|null $appUser
     */
    final public function setAppUser(?AppUser $appUser): void
    {
        if ($this->appUser && $appUser !== $this->appUser) {
            $this->appUser->removeAppUserFlag($this);
        }
        $this->appUser = $appUser;
        if ($this->appUser) {
            $this->appUser->addAppUserFlag($this);
        }
    }

    /**
     * Get flag of app user.
     * @return AppUserFlag|null
     */
    final public function getAppUserFlag(): ?AppUserFlag
    {
        return $this->appUserFlag;
    }

    /**
     * Set flag of app user.
     * @param AppUserFlag|null $appUserFlag
     */
    final public function setAppUserFlag(?AppUserFlag $appUserFlag): void
    {
        if ($this->appUserFlag && $appUserFlag !== $this->appUserFlag) {
            $this->appUserFlag->removeAppUserFlagConnection($this);
        }
        $this->appUserFlag = $appUserFlag;
        if ($this->appUserFlag) {
            $this->appUserFlag->addAppUserFlagConnection($this);
        }
    }
}
