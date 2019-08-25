<?php

namespace Zakjakub\OswisCoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\Collection;
use Zakjakub\OswisCoreBundle\Filter\SearchAnnotation as Searchable;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\NameableBasicTrait;

/**
 * Flag for app user.
 * @Doctrine\ORM\Mapping\Entity()
 * @Doctrine\ORM\Mapping\Table(name="core_app_user_flag")
 * @ApiResource()
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
class AppUserFlag
{
    use BasicEntityTrait;
    use NameableBasicTrait;

    /**
     * Connections to users which contains this flag.
     * @var Collection|null
     * @Doctrine\ORM\Mapping\OneToMany(
     *     targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUserFlagConnection",
     *     cascade={"all"},
     *     mappedBy="appUserFlag",
     *     fetch="EAGER"
     * )
     */
    protected $appUserFlagConnections;

    /**
     * Constructor for app user flag.
     *
     * @param Nameable|null $nameable
     */
    public function __construct(
        ?Nameable $nameable = null
    ) {
        $this->appUserFlagConnections = new ArrayCollection();
        $this->setFieldsFromNameable($nameable);
    }

    /**
     * @return Collection
     */
    final public function getAppUserFlagConnections(): Collection
    {
        return $this->appUserFlagConnections;
    }

    /**
     * @param AppUserFlagConnection|null $appUserFlagConnection
     */
    final public function addAppUserFlagConnection(?AppUserFlagConnection $appUserFlagConnection): void
    {
        if ($appUserFlagConnection && !$this->appUserFlagConnections->contains($appUserFlagConnection)) {
            $this->appUserFlagConnections->add($appUserFlagConnection);
            $appUserFlagConnection->setAppUserFlag($this);
        }
    }

    /**
     * @param AppUserFlagConnection|null $appUserFlagConnection
     */
    final public function removeAppUserFlagConnection(?AppUserFlagConnection $appUserFlagConnection): void
    {
        if (!$appUserFlagConnection) {
            return;
        }
        if ($this->appUserFlagConnections->removeElement($appUserFlagConnection)) {
            $appUserFlagConnection->setAppUserFlag(null);
        }
    }
}
