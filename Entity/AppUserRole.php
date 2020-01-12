<?php /** @noinspection MethodShouldBeFinalInspection */

/** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Zakjakub\OswisCoreBundle\Filter\SearchAnnotation as Searchable;
use Zakjakub\OswisCoreBundle\Traits\Entity\BasicEntityTrait;
use Zakjakub\OswisCoreBundle\Traits\Entity\NameableBasicTrait;

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
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $roleString = null;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUserRole", inversedBy="children", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected ?AppUserRole $parent = null;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUserRole", mappedBy="parent")
     */
    protected ?Collection $children = null;

    public function __construct(?Nameable $nameable = null, ?string $roleString = null, ?self $parent = null)
    {
        $this->parent = null;
        $this->children = new ArrayCollection();
        $this->setFieldsFromNameable($nameable);
        $this->roleString = $roleString;
        $this->setParent($parent);
    }

    public function getChildren(): Collection
    {
        return $this->children ?? new ArrayCollection();
    }

    /**
     * Get names of all contained roles.
     */
    public function getAllRoleNames(): Collection
    {
        return $this->getRoles()->map(fn(self $role) => $role->getRoleName());
    }

    /**
     * Get all contained roles.
     */
    public function getRoles(): Collection
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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $appUserRole): void
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
     * @example ROLE_USER
     */
    public function getRoleName(): string
    {
        return empty($this->getRoleString()) ? '' : 'ROLE_'.$this->getRoleString();
    }

    public function getRoleString(): string
    {
        return $this->roleString ?? '';
    }

    public function setRoleString(string $roleString): void
    {
        $this->roleString = $roleString ?? '';
    }

    public function removeChild(?self $appUserRole): void
    {
        if ($appUserRole && $this->children->removeElement($appUserRole)) {
            $appUserRole->setParent(null);
        }
    }

    public function addChild(?self $appUserRole): void
    {
        if ($appUserRole && !$this->children->contains($appUserRole)) {
            $this->children->add($appUserRole);
            $appUserRole->setParent($this);
        }
    }
}
