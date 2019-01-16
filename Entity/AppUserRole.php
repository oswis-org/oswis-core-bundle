<?php

namespace Zakjakub\OswisResourcesBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Zakjakub\OswisResourcesBundle\Filter\SearchAnnotation as Searchable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Zakjakub\OswisResourcesBundle\Traits\EntityNameableBasicTrait;

// Dummy statement -> use not deleted as unused.
\assert(Timestampable::class);

/**
 * Class AppUserRole
 * @ORM\Entity()
 * @ORM\Table(name="app_user_role")
 * @ApiResource(
 *   attributes={
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
 * @ApiFilter(OrderFilter::class)
 * @Searchable({
 *     "id",
 *     "name",
 *     "description",
 *     "roleString"
 * })
 */
class AppUserRole
{
    use EntityNameableBasicTrait;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $roleString;

    /**
     * @var AppUserRole|null
     * @ORM\ManyToOne(targetEntity="App\Entity\AppUserRole", inversedBy="children", fetch="EAGER")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\AppUserRole", mappedBy="parent")
     */
    protected $children;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\AppUserType", mappedBy="appUserRole")
     */
    protected $appUserTypes;

    public function __construct(
        ?Nameable $nameable = null,
        ?string $roleString = null,
        ?AppUserRole $parent = null
    ) {
        $this->children = new ArrayCollection();
        $this->appUserTypes = new ArrayCollection();
        $this->setFieldsFromNameable($nameable);
        $this->roleString = $roleString;
        $this->setParent($parent);
    }

    /**
     * @return Collection
     */
    final public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param AppUserType|null $appUserType
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
     * @param AppUserType|null $appUserType
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
     * @return Collection
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
     * @return AppUserRole|null
     */
    final public function getParent(): ?AppUserRole
    {
        return $this->parent;
    }

    /**
     * @param AppUserRole|null $appUserRole
     */
    final public function setParent(?AppUserRole $appUserRole): void
    {
        if (null !== $this->parent) {
            $this->parent->removeChild($this);
        }
        if ($appUserRole && $this->parent !== $appUserRole) {
            $appUserRole->addChild($this);
            $this->parent = $appUserRole;
        }
    }

    final public function getRoleName(): string
    {
        if (!$this->getRoleString() || $this->getRoleString() === '') {
            return '';
        }

        return 'ROLE_'.$this->getRoleString();
    }

    /**
     * @return string
     */
    final public function getRoleString(): string
    {
        return $this->roleString ?? '';
    }

    /**
     * @param string $roleString
     */
    final public function setRoleString(string $roleString): void
    {
        $this->roleString = $roleString ?? '';
    }

    /**
     * @param AppUserRole|null $appUserRole
     */
    final public function removeChild(?AppUserRole $appUserRole): void
    {
        if (!$appUserRole) {
            return;
        }
        if ($this->children->removeElement($appUserRole)) {
            $appUserRole->setParent(null);
        }
    }

    /**
     * @param AppUserRole|null $appUserRole
     */
    final public function addChild(?AppUserRole $appUserRole): void
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
