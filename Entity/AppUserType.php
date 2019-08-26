<?php

namespace Zakjakub\OswisCoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Zakjakub\OswisCoreBundle\Filter\SearchAnnotation as Searchable;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\NameableBasicTrait;

/**
 * Type of user (customer, manager, admin etc.).
 * @Doctrine\ORM\Mapping\Entity
 * @Doctrine\ORM\Mapping\Table(name="core_app_user_type")
 * @ApiResource(
 *   attributes={
 *     "filters"={"search"},
 *     "access_control"="is_granted('ROLE_MANAGER')",
 *     "normalization_context"={"groups"={"app_user_types_get"}},
 *     "denormalization_context"={"groups"={"app_user_types_post"}}
 *   },
 *   collectionOperations={
 *     "get"={
 *       "access_control"="is_granted('ROLE_MANAGER')",
 *       "normalization_context"={"groups"={"app_user_types_get"}},
 *     },
 *     "post"={
 *       "access_control"="is_granted('ROLE_ROOT')",
 *       "denormalization_context"={"groups"={"app_user_types_post"}}
 *     }
 *   },
 *   itemOperations={
 *     "get"={
 *       "access_control"="is_granted('ROLE_MANAGER')",
 *       "normalization_context"={"groups"={"app_user_type_get"}},
 *     },
 *     "put"={
 *       "access_control"="is_granted('ROLE_ROOT')",
 *       "denormalization_context"={"groups"={"app_user_type_put"}}
 *     },
 *     "delete"={
 *       "access_control"="is_granted('ROLE_ROOT')",
 *       "denormalization_context"={"groups"={"app_user_type_delete"}}
 *     }
 *   }
 * )
 * @ApiFilter(OrderFilter::class, properties={
 *     "id": "ASC",
 *     "dateTime": "ASC",
 *     "slug",
 *     "name",
 *     "shortName",
 *     "description",
 *     "note",
 *     "adminUser"
 * })
 * @ApiFilter(ExistsFilter::class, properties={"adminUser"})
 * @Searchable({
 *     "id",
 *     "slug",
 *     "name",
 *     "shortName",
 *     "description",
 *     "note"
 * })
 * @author Jakub Zak <mail@jakubzak.eu>
 * @Doctrine\ORM\Mapping\Cache(usage="NONSTRICT_READ_WRITE", region="core_app_user")
 */
class AppUserType
{
    use BasicEntityTrait;
    use NameableBasicTrait;

    /**
     * App users using this role.
     * @var Collection
     * @Doctrine\ORM\Mapping\OneToMany(
     *     targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUser",
     *     mappedBy="appUserType"
     * )
     */
    protected $appUsers;

    /**
     * Contained app user role.
     * @var AppUserRole
     * @Doctrine\ORM\Mapping\ManyToOne(
     *     targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUserRole",
     *     inversedBy="appUserTypes",
     *     fetch="EAGER"
     * )
     * @Doctrine\ORM\Mapping\JoinColumn(name="user_role_id", referencedColumnName="id")
     * @todo Refactor for use of multiple roles.
     */
    protected $appUserRole;

    /**
     * User has access to administration/IS.
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     * @todo Make property used (probably has not effect now).
     */
    protected $adminUser;

    /**
     * Constructor of app user type.
     *
     * @param Nameable|null $nameable
     * @param AppUserRole   $appUserRole
     * @param bool|null     $adminUser
     */
    public function __construct(
        ?Nameable $nameable = null,
        ?AppUserRole $appUserRole = null,
        ?bool $adminUser = false
    ) {
        $this->setFieldsFromNameable($nameable);
        $this->appUsers = new ArrayCollection();
        $this->appUserRole = $appUserRole;
        $this->adminUser = $adminUser;
    }

    /**
     * User has access to administration/IS.
     * @return bool
     * @todo Make property used (probably has not effect now).
     */
    final public function getAdminUser(): bool
    {
        return $this->adminUser ?? false;
    }

    /**
     * Set if user has access to administration/IS.
     *
     * @param bool|null $adminUser
     *
     * @todo Make property used (probably has not effect now).
     */
    final public function setAdminUser(?bool $adminUser): void
    {
        $this->adminUser = $adminUser ?? null;
    }

    /**
     * Add app user of this type.
     *
     * @param AppUser|null $appUser
     */
    final public function addAppUser(?AppUser $appUser): void
    {
        if (!$appUser) {
            return;
        }
        if (!$this->appUsers->contains($appUser)) {
            $this->appUsers->add($appUser);
            $appUser->setAppUserType($this);
        }
    }

    /**
     * Remove app user from this type.
     *
     * @param AppUser|null $appUser
     */
    final public function removeAppUser(?AppUser $appUser): void
    {
        if (!$appUser) {
            return;
        }
        if ($this->appUsers->removeElement($appUser) && $appUser->getAppUserType() === $this) {
            $appUser->setAppUserType(null);
        }
    }

    /**
     * Get app users of this type.
     * @return Collection
     */
    final public function getAppUsers(): Collection
    {
        return $this->appUsers;
    }

    /**
     * Get name/string of role of this type.
     * @return string
     */
    final public function getRoleName(): string
    {
        return $this->getAppUserRole() ? $this->getAppUserRole()->getRoleName() : '';
    }

    /**
     * Get role of this type.
     * @return AppUserRole
     */
    final public function getAppUserRole(): ?AppUserRole
    {
        return $this->appUserRole;
    }

    /**
     * Set role of this type.
     *
     * @param AppUserRole|null $appUserRole
     */
    final public function setAppUserRole(?AppUserRole $appUserRole): void
    {
        if (null !== $this->appUserRole) {
            $this->appUserRole->removeAppUserType($this);
        }
        if ($appUserRole && $this->appUserRole !== $appUserRole) {
            $appUserRole->addAppUserType($this);
            $this->appUserRole = $appUserRole;
        }
    }

    /**
     * Get names of all roles contained in this type.
     * @return Collection
     */
    final public function getAllRoleNames(): Collection
    {
        return $this->getAppUserRole() ? $this->getAppUserRole()->getAllRoleNames() : new ArrayCollection();
    }
}
