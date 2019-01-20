<?php

namespace Zakjakub\OswisCoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Zakjakub\OswisCoreBundle\Filter\SearchAnnotation as Searchable;
use Zakjakub\OswisCoreBundle\Traits\Entity\NameableBasicTrait;

// Dummy statement -> use not deleted as unused.
\assert(Timestampable::class);

/**
 * Class AppUserType (customer, manager, admin etc.)
 * @ORM\Entity
 * @ORM\Table(name="app_user_type")
 * @ApiResource(
 *   attributes={
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
 * @ApiFilter(OrderFilter::class)
 * @ApiFilter(ExistsFilter::class, properties={"adminUser"})
 * @Searchable({
 *     "id",
 *     "name",
 *     "description",
 *     "singleNote"
 * })
 */
class AppUserType
{
    use NameableBasicTrait;

    /**
     * App users using this role.
     * @var Collection
     * @ORM\OneToMany(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUser", mappedBy="appUserType")
     */
    protected $appUsers;

    /**
     * App user ROLE string, without ROLE_ (EVERYBODY, CUSTOMER, USER, USER_ADVANCED, FACILITY_MANAGER, MANAGER, ADMIN, ROOT).
     * @var AppUserRole
     * @ORM\ManyToOne(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUserRole", inversedBy="appUserTypes", fetch="EAGER")
     * @ORM\JoinColumn(name="user_role_id", referencedColumnName="id")
     */
    protected $appUserRole;

    /**
     * True if user has access to administration (IS).
     * @var bool|null
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $adminUser;

    /**
     * AppUserType constructor.
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
     * @return bool
     */
    final public function getAdminUser(): bool
    {
        return $this->adminUser ?? false;
    }

    /**
     * @param bool|null $adminUser
     */
    final public function setAdminUser(?bool $adminUser): void
    {
        $this->adminUser = $adminUser ?? null;
    }

    /**
     * Add person.
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
     * Remove person.
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
     * @return Collection
     */
    final public function getAppUsers(): Collection
    {
        return $this->appUsers;
    }

    /**
     * @return string
     */
    final public function getRoleName(): string
    {
        return $this->getAppUserRole() ? $this->getAppUserRole()->getRoleName() : '';
    }

    /**
     * @return AppUserRole
     */
    final public function getAppUserRole(): ?AppUserRole
    {
        return $this->appUserRole;
    }

    /**
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
     * @return Collection
     */
    final public function getAllRoleNames(): Collection
    {
        return $this->getAppUserRole() ? $this->getAppUserRole()->getAllRoleNames() : new ArrayCollection();
    }
}
