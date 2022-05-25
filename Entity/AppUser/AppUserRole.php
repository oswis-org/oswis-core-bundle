<?php

/**
 * @noinspection PhpUnused
 * @noinspection PropertyCanBePrivateInspection
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\AppUser;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use InvalidArgumentException;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Filter\SearchAnnotation as Searchable;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;
use OswisOrg\OswisCoreBundle\Interfaces\Common\NameableInterface;
use OswisOrg\OswisCoreBundle\Repository\AppUserRoleRepository;
use OswisOrg\OswisCoreBundle\Traits\Common\NameableTrait;

/**
 * Role of app user.
 * @author Jakub Zak <mail@jakubzak.eu>
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
 * @ApiResource(
 *   attributes={
 *     "filters"={"search"},
 *     "security"="is_granted('ROLE_ADMIN')",
 *     "normalization_context"={"groups"={"app_user_roles_get"}, "enable_max_depth"=true},
 *     "denormalization_context"={"groups"={"app_user_roles_post"}, "enable_max_depth"=true}
 *   },
 *   collectionOperations={
 *     "get"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"app_user_roles_get"}, "enable_max_depth"=true},
 *     },
 *     "post"={
 *       "security"="is_granted('ROLE_ROOT')",
 *       "denormalization_context"={"groups"={"app_user_roles_post"}, "enable_max_depth"=true}
 *     }
 *   },
 *   itemOperations={
 *     "get"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"app_user_role_get"}, "enable_max_depth"=true},
 *     },
 *     "put"={
 *       "security"="is_granted('ROLE_ROOT')",
 *       "denormalization_context"={"groups"={"app_user_role_put"}, "enable_max_depth"=true}
 *     }
 *   }
 * )
 */
#[Entity(repositoryClass: AppUserRoleRepository::class)]
#[Table(name: 'core_app_user_role')]
#[Cache(usage: 'NONSTRICT_READ_WRITE', region: 'core_app_user')]
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
    public const ROLES_PARENT
        = [
            self::ROLE_CUSTOMER => self::ROLE_EVERYBODY,
            self::ROLE_USER     => self::ROLE_CUSTOMER,
            self::ROLE_MEMBER   => self::ROLE_USER,
            self::ROLE_MANAGER  => self::ROLE_MEMBER,
            self::ROLE_ADMIN    => self::ROLE_MANAGER,
            self::ROLE_ROOT     => self::ROLE_ADMIN,
        ];

    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[Column(type: 'string', nullable: true)]
    protected ?string $roleString = null;

    #[ManyToOne(targetEntity: self::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'parent_id', referencedColumnName: 'id')]
    #[ApiFilter(SearchFilter::class, properties: ["parent.id" => "exact", "parent.name" => "ipartial", "parent.slug" => "ipartial"])]
    #[ApiFilter(OrderFilter::class)]
    protected ?self $parent = null;

    /**
     * AppUserRole constructor.
     *
     * @param  Nameable|null  $nameable
     * @param  string|null  $roleString
     * @param  \OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserRole|null  $parent
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

    /** Get names of all contained roles. */
    public function getAllRoleNames(): Collection
    {
        return $this->getRoles()->map(fn(mixed $role) => $role instanceof self ? $role->getRoleName() : null);
    }

    /**
     * Get all contained roles.
     * @return Collection<self>
     */
    public function getRoles(): Collection
    {
        /** @var Collection<self> $roles */
        $roles = new ArrayCollection([$this]);
        foreach ($this->getParent()?->getRoles() ?? new ArrayCollection() as $role) {
            if (!$roles->contains($role)) {
                $roles->add($role);
            }
        }

        return $roles;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @param  self|null  $appUserRole
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
        $this->roleString = $roleString;
    }
}
