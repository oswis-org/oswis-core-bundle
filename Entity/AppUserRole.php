<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Zakjakub\OswisCoreBundle\Filter\SearchAnnotation as Searchable;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\NameableBasicTrait;
use function assert;

/**
 * Role of app user.
 *
 * @Doctrine\ORM\Mapping\Entity()
 * @Doctrine\ORM\Mapping\Table(name="core_app_user_role")
 * @ApiResource(
 *   attributes={
 *     "filters"={"search"},
 *     "access_control"="is_granted('ROLE_MANAGER')",
 *     "normalization_context"={"groups"={"app_user_roles_get"}},
 *     "denormalization_context"={"groups"={"app_user_roles_post"}}
 *   },
 *   collectionOperations={
 *     "get"={
 *       "access_control"="is_granted('ROLE_MANAGER')",
 *       "normalization_context"={"groups"={"app_user_roles_get"}},
 *     },
 *     "post"={
 *       "access_control"="is_granted('ROLE_ROOT')",
 *       "denormalization_context"={"groups"={"app_user_roles_post"}}
 *     }
 *   },
 *   itemOperations={
 *     "get"={
 *       "access_control"="is_granted('ROLE_MANAGER')",
 *       "normalization_context"={"groups"={"app_user_role_get"}},
 *     },
 *     "put"={
 *       "access_control"="is_granted('ROLE_ROOT')",
 *       "denormalization_context"={"groups"={"app_user_role_put"}}
 *     },
 *     "delete"={
 *       "access_control"="is_granted('ROLE_ROOT')",
 *       "denormalization_context"={"groups"={"app_user_role_delete"}}
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
 *     "roleString"
 * })
 * @Searchable({
 *     "id",
 *     "slug",
 *     "name",
 *     "shortName",
 *     "description",
 *     "note",
 *     "roleString"
 * })
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 * @Doctrine\ORM\Mapping\Cache(usage="NONSTRICT_READ_WRITE", region="core_app_user")
 */
class AppUserRole
{
    use BasicEntityTrait;
    use NameableBasicTrait;

    /**
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $roleString;

    /**
     * Parent role (also included in this role).
     *
     * @var AppUserRole|null
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUserRole", inversedBy="children", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected ?AppUserRole $parent;

    /**
     * Child roles (which includes this role).
     *
     * @var Collection
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUserRole", mappedBy="parent")
     */
    protected $children;

    /**
     * Types of app users containing this role.
     *
     * @var Collection
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUserType", mappedBy="appUserRole")
     */
    protected $appUserTypes;

    /**
     * AppUserRole constructor.
     *
     * @param AppUserRole|null $parent
     */
    public function __construct(
        ?Nameable $nameable = null,
        ?string $roleString = null,
        ?self $parent = null
    ) {
        $this->parent = null;
        $this->children = new ArrayCollection();
        $this->appUserTypes = new ArrayCollection();
        $this->setFieldsFromNameable($nameable);
        $this->roleString = $roleString;
        $this->setParent($parent);
    }

    /**
     * Get child roles.
     */
    final public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * Remove app user type.
     */
    final public function removeAppUserType(?AppUserType $appUserType): void
    {
        if (!$appUserType) {
            return;
        }
        if ($this->appUserTypes->removeElement($appUserType)) {
            $appUserType->setAppUserRole(null);
        }
    }

    /**
     * Add app user type.
     */
    final public function addAppUserType(?AppUserType $appUserType): void
    {
        if (!$appUserType) {
            return;
        }
        if (!$this->appUserTypes->contains($appUserType)) {
            $this->appUserTypes->add($appUserType);
            $appUserType->setAppUserRole($this);
        }
    }

    /**
     * Get names of all contained roles.
     */
    final public function getAllRoleNames(): Collection
    {
        $roleNames = new ArrayCollection();
        foreach ($this->getRoles() as $appUserRole) {
            assert($appUserRole instanceof self);
            if (!$roleNames->contains($appUserRole)) {
                $roleNames->add($appUserRole->getRoleName());
            }
        }

        return $roleNames;
    }

    /**
     * Get all contained roles.
     */
    final public function getRoles(): Collection
    {
        $roles = new ArrayCollection([$this]);
        if ($this->getParent()) {
            foreach ($this->getParent()->getRoles() as $role) {
                if (!$roles->contains($role)) {
                    $roles->add($role);
                }
            }
        }

        return $roles;
    }

    /**
     * Get parent role (or null of parent is not set).
     */
    final public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * Set parent role.
     *
     * @param AppUserRole|null $appUserRole
     */
    final public function setParent(?self $appUserRole): void
    {
        if (null !== $this->parent) {
            $this->parent->removeChild($this);
        }
        if ($appUserRole && $this->parent !== $appUserRole) {
            $appUserRole->addChild($this);
            $this->parent = $appUserRole;
        }
    }

    /**
     * Get name/string of role.
     *
     * @example ROLE_USER
     */
    final public function getRoleName(): string
    {
        if (!$this->getRoleString() || '' === $this->getRoleString()) {
            return '';
        }

        return 'ROLE_'.$this->getRoleString();
    }

    final public function getRoleString(): string
    {
        return $this->roleString ?? '';
    }

    final public function setRoleString(string $roleString): void
    {
        $this->roleString = $roleString ?? '';
    }

    /**
     * Remove child role from this role.
     *
     * @param AppUserRole|null $appUserRole
     */
    final public function removeChild(?self $appUserRole): void
    {
        if (!$appUserRole) {
            return;
        }
        if ($this->children->removeElement($appUserRole)) {
            $appUserRole->setParent(null);
        }
    }

    /**
     * Add child role to this role.
     *
     * @param AppUserRole|null $appUserRole
     */
    final public function addChild(?self $appUserRole): void
    {
        if (!$appUserRole) {
            return;
        }
        if (!$this->children->contains($appUserRole)) {
            $this->children->add($appUserRole);
            $appUserRole->setParent($this);
        }
    }
}
