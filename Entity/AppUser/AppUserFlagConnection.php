<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\AppUser;

use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use OswisOrg\OswisCoreBundle\Interfaces\Common\BasicInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\BasicTrait;

/**
 * Connection between app user and used app user flag.
 *
 * @ApiPlatform\Metadata\ApiFilter(ApiPlatform\Doctrine\Orm\Filter\OrderFilter::class)
 * @author Jakub Zak <mail@jakubzak.eu>
 */
#[Entity]
#[Table(name: 'core_app_user_flag_connection')]
#[Cache(usage: 'NONSTRICT_READ_WRITE', region: 'core_app_user')]
class AppUserFlagConnection implements BasicInterface
{
    use BasicTrait;

    #[ManyToOne(targetEntity: AppUserFlag::class, fetch: 'EAGER')]
    #[JoinColumn(nullable: true)]
    protected ?AppUserFlag $appUserFlag = null;

    #[ManyToOne(targetEntity: AppUser::class, inversedBy: 'appUserFlags')]
    #[JoinColumn(nullable: true)]
    protected ?AppUser $appUser = null;

    public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    public function setAppUser(?AppUser $appUser): void
    {
        if (null !== $this->appUser && $appUser !== $this->appUser) {
            $this->appUser->removeAppUserFlag($this);
        }
        $this->appUser = $appUser;
        $this->appUser?->addAppUserFlag($this);
    }

    public function getAppUserFlag(): ?AppUserFlag
    {
        return $this->appUserFlag;
    }

    public function setAppUserFlag(?AppUserFlag $appUserFlag): void
    {
        $this->appUserFlag = $appUserFlag;
    }
}
