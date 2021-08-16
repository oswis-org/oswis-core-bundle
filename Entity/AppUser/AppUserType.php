<?php

/**
 * @noinspection PropertyCanBePrivateInspection
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\AppUser;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Interfaces\Common\NameableInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\NameableTrait;

/**
 * Form of user (customer, manager, admin etc.).
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="OswisOrg\OswisCoreBundle\Repository\AppUserTypeRepository")
 * @Doctrine\ORM\Mapping\Table(name="core_app_user_type")
 * @ApiPlatform\Core\Annotation\ApiResource(
 *   attributes={
 *     "filters"={"search"},
 *     "security"="is_granted('ROLE_MANAGER')"
 *   },
 *   collectionOperations={
 *     "get"={
 *       "security"="is_granted('ROLE_MANAGER')",
 *       "normalization_context"={"groups"={"entities_get", "app_user_types_get"}, "enable_max_depth"=true},
 *     },
 *     "post"={
 *       "security"="is_granted('ROLE_ROOT')",
 *       "denormalization_context"={"groups"={"entities_post", "app_user_types_post"}, "enable_max_depth"=true}
 *     }
 *   },
 *   itemOperations={
 *     "get"={
 *       "security"="is_granted('ROLE_MANAGER')",
 *       "normalization_context"={"groups"={"entity_get", "app_user_type_get"}, "enable_max_depth"=true},
 *     },
 *     "put"={
 *       "security"="is_granted('ROLE_ROOT')",
 *       "denormalization_context"={"groups"={"entity_put", "app_user_type_put"}, "enable_max_depth"=true}
 *     }
 *   }
 * )
 * @OswisOrg\OswisCoreBundle\Filter\SearchAnnotation({
 *     "id",
 *     "slug",
 *     "name",
 *     "shortName",
 *     "description",
 *     "note"
 * })
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 * @Doctrine\ORM\Mapping\Cache(usage="NONSTRICT_READ_WRITE", region="core_app_user")
 */
class AppUserType implements NameableInterface
{
    use NameableTrait;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserRole", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(name="app_user_role_id", referencedColumnName="id")
     * @ApiPlatform\Core\Annotation\ApiFilter(
     *     ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class,
     *     properties={"appUserRole.id": "exact", "appUserRole.name": "ipartial", "appUserRole.slug": "ipartial"}
     * )
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     * @todo Refactor for use of multiple roles.
     */
    protected ?AppUserRole $appUserRole = null;

    /**
     * Indicates that user has access to administration/IS.
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=false, options={"default": false})
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     * @todo Make property used (probably has not effect now).
     */
    protected bool $adminUser = false;

    public function __construct(?Nameable $entity = null, ?AppUserRole $appUserRole = null, ?bool $adminUser = false)
    {
        $this->setAppUserRole($appUserRole);
        $this->setAdminUser($adminUser);
        $this->setFieldsFromNameable($entity);
    }

    /**
     * User has access to administration/IS.
     *
     * @todo Make property used (probably has not effect now).
     */
    public function getAdminUser(): bool
    {
        return $this->adminUser ?? false;
    }

    /**
     * Set if user has access to administration/IS.
     *
     * @todo Make property used (probably has not effect now).
     */
    public function setAdminUser(?bool $adminUser): void
    {
        $this->adminUser = $adminUser ?? false;
    }

    /**
     * Get name/string of role of this type.
     */
    public function getRoleName(): string
    {
        return $this->getAppUserRole()?->getRoleName() ?? '';
    }

    /**
     * Get role of this type.
     *
     * @return AppUserRole
     */
    public function getAppUserRole(): ?AppUserRole
    {
        return $this->appUserRole;
    }

    /**
     * Set role of this type.
     */
    public function setAppUserRole(?AppUserRole $appUserRole): void
    {
        $this->appUserRole = $appUserRole;
    }

    /**
     * Get names of all roles contained in this type.
     */
    public function getAllRoleNames(): Collection
    {
        return $this->getAppUserRole()?->getAllRoleNames() ?? new ArrayCollection();
    }
}
