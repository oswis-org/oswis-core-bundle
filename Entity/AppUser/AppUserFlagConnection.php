<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Entity\AppUser;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use OswisOrg\OswisCoreBundle\Filter\SearchAnnotation as Searchable;
use OswisOrg\OswisCoreBundle\Interfaces\Common\BasicEntityInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\BasicEntityTrait;

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
 * @author Jakub Zak <mail@jakubzak.eu>
 * @Doctrine\ORM\Mapping\Cache(usage="NONSTRICT_READ_WRITE", region="core_app_user")
 */
class AppUserFlagConnection implements BasicEntityInterface
{
    use BasicEntityTrait;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserFlag", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=true)
     */
    protected ?AppUserFlag $appUserFlag = null;

    public function getAppUserFlag(): ?AppUserFlag
    {
        return $this->appUserFlag;
    }

    public function setAppUserFlag(?AppUserFlag $appUserFlag): void
    {
        $this->appUserFlag = $appUserFlag;
    }
}
