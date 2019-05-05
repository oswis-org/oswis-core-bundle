<?php

namespace Zakjakub\OswisCoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Zakjakub\OswisCoreBundle\Filter\SearchAnnotation as Searchable;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;

/**
 * @Doctrine\ORM\Mapping\Entity()
 * @Doctrine\ORM\Mapping\Table(name="job_fair_flag_in_job_fair_user")
 * @ApiResource()
 * @ApiFilter(OrderFilter::class)
 * @Searchable({
 *     "id",
 *     "name",
 *     "description",
 *     "note"
 * })
 */
class AppUserFlagConnection
{
    use BasicEntityTrait;

    /**
     * Dummy "selected" property for forms.
     * @var bool
     */
    public $selected;

    /**
     * Flag.
     * @var AppUserFlag|null
     * @Doctrine\ORM\Mapping\ManyToOne(
     *     targetEntity="Zakjakub\OswisJobFairBundle\Entity\JobFairUserFlag",
     *     inversedBy="jobFairUserFlagConnections",
     *     fetch="EAGER"
     * )
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=true)
     */
    protected $appUserFlag;

    /**
     * @var AppUser|null
     * @Doctrine\ORM\Mapping\ManyToOne(
     *     targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUser",
     *     inversedBy="appUserFlags",
     *     fetch="EAGER"
     * )
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=true)
     */
    protected $appUser;

    final public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

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

    final public function getAppUserFlag(): ?AppUserFlag
    {
        return $this->appUserFlag;
    }

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
