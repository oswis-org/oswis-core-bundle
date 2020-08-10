<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AppUser;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Filter\SearchAnnotation as Searchable;
use OswisOrg\OswisCoreBundle\Interfaces\Common\NameableInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\NameableTrait;

/**
 * Role of app user.
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="OswisOrg\OswisCoreBundle\Repository\AppUserRoleRepository")
 * @Doctrine\ORM\Mapping\Table(name="core_app_user_role")
 * @ApiResource(
 *   attributes={
 *     "filters"={"search"},
 *     "access_control"="is_granted('ROLE_ADMIN')",
 *     "normalization_context"={"groups"={"app_user_roles_get"}, "enable_max_depth"=true},
 *     "denormalization_context"={"groups"={"app_user_roles_post"}, "enable_max_depth"=true}
 *   },
 *   collectionOperations={
 *     "get"={
 *       "access_control"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"app_user_roles_get"}, "enable_max_depth"=true},
 *     },
 *     "post"={
 *       "access_control"="is_granted('ROLE_ROOT')",
 *       "denormalization_context"={"groups"={"app_user_roles_post"}, "enable_max_depth"=true}
 *     }
 *   },
 *   itemOperations={
 *     "get"={
 *       "access_control"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"app_user_role_get"}, "enable_max_depth"=true},
 *     },
 *     "put"={
 *       "access_control"="is_granted('ROLE_ROOT')",
 *       "denormalization_context"={"groups"={"app_user_role_put"}, "enable_max_depth"=true}
 *     }
 *   }
 * )
 * @Searchable({
 *     "id",
 *     "slug",
 *     "forcedSlug",
 *     "name",
 *     "shortName",
 *     "description",
 *     "note",
 *     "roleString"
 * })
 * @author Jakub Zak <mail@jakubzak.eu>
 * @Doctrine\ORM\Mapping\Cache(usage="NONSTRICT_READ_WRITE", region="core_app_user")
 */
class AppUserRole implements NameableInterface
{
    use NameableTrait;

    public const ROLE_EVERYBODY = 'ROLE_EVERYBODY';
    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_MEMBER = 'ROLE_MEMBER';
    public const ROLE_MANAGER = 'ROLE_MANAGER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_ROOT = 'ROLE_ROOT';

    public const ROLES_PARENT = [
        self::ROLE_CUSTOMER => self::ROLE_EVERYBODY,
        self::ROLE_USER     => self::ROLE_CUSTOMER,
        self::ROLE_MEMBER   => self::ROLE_USER,
        self::ROLE_MANAGER  => self::ROLE_MEMBER,
        self::ROLE_ADMIN    => self::ROLE_MANAGER,
        self::ROLE_ROOT     => self::ROLE_ADMIN,
    ];

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $roleString = null;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserRole", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(name="parent_id", referencedColumnName="id")
     * @ApiPlatform\Core\Annotation\ApiFilter(
     *     ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class,
     *     properties={"parent.id": "exact", "parent.name": "ipartial", "parent.slug": "ipartial"}
     * )
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?AppUserRole $parent = null;

    /**
     * AppUserRole constructor.
     *
     * @param Nameable|null    $nameable
     * @param string|null      $roleString
     * @param AppUserRole|null $parent
     *
     * @throws InvalidArgumentException
     */
    public function __construct(?Nameable $nameable = null, ?string $roleString = null, ?self $parent = null)
    {
        $this->parent = null;
        $this->roleString = $roleString;
        $this->setFieldsFromNameable($nameable);
        $this->setParent($parent);
    }

//    public function getChildren(): Collection
//    {
//        return $this->children ?? new ArrayCollection();
//    }
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

    /**
     * @param AppUserRole|null $appUserRole
     *
     * @throws InvalidArgumentException
     */
    public function setParent(?self $appUserRole): void
    {
        if ($this === $appUserRole) {
            throw new InvalidArgumentException('Role nemůže být nadřazenou sama sobě.');
        }
        $this->parent = $appUserRole;
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
}
