<?php

/**
 * @noinspection PropertyCanBePrivateInspection
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\AppUser;

use OswisOrg\OswisCoreBundle\Filter\SearchAnnotation;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;
use OswisOrg\OswisCoreBundle\Interfaces\Common\NameableInterface;
use OswisOrg\OswisCoreBundle\Repository\AppUserTypeRepository;
use OswisOrg\OswisCoreBundle\Traits\Common\NameableTrait;

/**
 * Form of user (customer, manager, admin etc.).
 * @author Jakub Zak <mail@jakubzak.eu>
 */
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['entities_get', 'app_user_types_get'], 'enable_max_depth' => true],
            security: "is_granted('ROLE_MANAGER')",
        ),
        new Post(
            denormalizationContext: ['groups' => ['entities_post', 'app_user_types_post'], 'enable_max_depth' => true],
            security: "is_granted('ROLE_ROOT')",
        ),
        new Get(
            normalizationContext: ['groups' => ['entity_get', 'app_user_type_get'], 'enable_max_depth' => true],
            security: "is_granted('ROLE_MANAGER')",
        ),
        new Put(
            denormalizationContext: ['groups' => ['entity_put', 'app_user_type_put'], 'enable_max_depth' => true],
            security: "is_granted('ROLE_ROOT')",
        ),
    ],
    filters: ['search'],
    security: "is_granted('ROLE_MANAGER')",
)]
#[SearchAnnotation(['id', 'slug', 'name', 'shortName', 'description', 'note'])]
#[Entity(repositoryClass: AppUserTypeRepository::class)]
#[Table(name: 'core_app_user_type')]
#[Cache(usage: 'NONSTRICT_READ_WRITE', region: 'core_app_user')]
class AppUserType implements NameableInterface
{
    use NameableTrait;

    #[ManyToOne(targetEntity: AppUserRole::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'app_user_role_id', referencedColumnName: 'id')]
    #[ApiFilter(SearchFilter::class, properties: [
        "appUserRole.id"   => "exact",
        "appUserRole.name" => "ipartial",
        "appUserRole.slug" => "ipartial",
    ])]
    #[ApiFilter(OrderFilter::class)]
    protected ?AppUserRole $appUserRole = null;

    /** Indicates that user has access to administration/IS. */
    #[Column(type: 'boolean', nullable: false, options: ['default' => false])]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(BooleanFilter::class)]
    protected bool $adminUser = false;

    public function __construct(?Nameable $entity = null, ?AppUserRole $appUserRole = null, ?bool $adminUser = false)
    {
        $this->setAppUserRole($appUserRole);
        $this->setAdminUser($adminUser);
        $this->setFieldsFromNameable($entity);
    }

    /** User has access to administration/IS. */
    public function getAdminUser(): bool
    {
        return $this->adminUser;
    }

    /** Set if user has access to administration/IS. */
    public function setAdminUser(?bool $adminUser): void
    {
        $this->adminUser = $adminUser ?? false;
    }

    /** Get name/string of role of this type. */
    public function getRoleName(): string
    {
        return $this->getAppUserRole()?->getRoleName() ?? '';
    }

    /** Get role of this type. */
    public function getAppUserRole(): ?AppUserRole
    {
        return $this->appUserRole;
    }

    /** Set role of this type. */
    public function setAppUserRole(?AppUserRole $appUserRole): void
    {
        $this->appUserRole = $appUserRole;
    }

    /** Get names of all roles contained in this type. */
    public function getAllRoleNames(): Collection
    {
        return $this->getAppUserRole()?->getAllRoleNames() ?? new ArrayCollection();
    }
}
