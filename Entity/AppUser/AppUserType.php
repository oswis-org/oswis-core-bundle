<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Entity\AppUser;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Filter\SearchAnnotation as Searchable;
use OswisOrg\OswisCoreBundle\Interfaces\Common\NameableEntityInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\NameableBasicTrait;

/**
 * Form of user (customer, manager, admin etc.).
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="OswisOrg\OswisCoreBundle\Repository\AppUserTypeRepository")
 * @Doctrine\ORM\Mapping\Table(name="core_app_user_type")
 * @ApiResource(
 *   attributes={
 *     "filters"={"search"},
 *     "access_control"="is_granted('ROLE_MANAGER')",
 *     "normalization_context"={"groups"={"app_user_types_get"}, "enable_max_depth"=true},
 *     "denormalization_context"={"groups"={"app_user_types_post"}, "enable_max_depth"=true}
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
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 * @Doctrine\ORM\Mapping\Cache(usage="NONSTRICT_READ_WRITE", region="core_app_user")
 */
class AppUserType implements NameableEntityInterface
{
    use NameableBasicTrait;

    /**
     * Contained app user role.
     *
     * @Doctrine\ORM\Mapping\ManyToOne(
     *     targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserRole",
     *     fetch="EAGER"
     * )
     * @Doctrine\ORM\Mapping\JoinColumn(name="user_role_id", referencedColumnName="id")
     * @todo Refactor for use of multiple roles.
     */
    protected ?AppUserRole $appUserRole = null;

    /**
     * User has access to administration/IS.
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     * @todo Make property used (probably has not effect now).
     */
    protected ?bool $adminUser = null;

    public function __construct(?Nameable $nameable = null, ?AppUserRole $appUserRole = null, ?bool $adminUser = false)
    {
        $this->setAppUserRole($appUserRole);
        $this->setAdminUser($adminUser);
        $this->setFieldsFromNameable($nameable);
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
        $this->adminUser = $adminUser ?? null;
    }

    /**
     * Get name/string of role of this type.
     */
    public function getRoleName(): string
    {
        return $this->getAppUserRole() ? $this->getAppUserRole()->getRoleName() : '';
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
        return $this->getAppUserRole() ? $this->getAppUserRole()->getAllRoleNames() : new ArrayCollection();
    }
}
