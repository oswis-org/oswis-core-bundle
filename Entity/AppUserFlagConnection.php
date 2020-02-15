<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Zakjakub\OswisCoreBundle\Filter\SearchAnnotation as Searchable;
use Zakjakub\OswisCoreBundle\Interfaces\BasicEntityInterface;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;

/**
 * Connection between app user and used app user flag.
 *
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
 * @Doctrine\ORM\Mapping\Cache(usage="NONSTRICT_READ_WRITE", region="core_app_user")
 */
class AppUserFlagConnection implements BasicEntityInterface
{
    use BasicEntityTrait;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUserFlag")
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=true)
     */
    protected ?AppUserFlag $appUserFlag = null;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUser", inversedBy="appUserFlags")
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=true)
     */
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
        if (null !== $this->appUser) {
            $this->appUser->addAppUserFlag($this);
        }
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
