<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractAppUser;
use OswisOrg\OswisCoreBundle\Filter\SearchAnnotation as Searchable;

/**
 * User of application.
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="OswisOrg\OswisCoreBundle\Repository\AppUserRepository")
 * @Doctrine\ORM\Mapping\Table(name="core_app_user")
 * @ApiResource(
 *   attributes={
 *     "filters"={"search"},
 *     "access_control"="is_granted('ROLE_USER')",
 *     "normalization_context"={"groups"={"app_users_get"}},
 *     "denormalization_context"={"groups"={"app_users_post"}}
 *   },
 *   collectionOperations={
 *     "get"={
 *       "access_control"="is_granted('ROLE_MANAGER')",
 *       "normalization_context"={"groups"={"app_users_get"}},
 *     },
 *     "post"={
 *       "access_control"="is_granted('ROLE_ADMIN')",
 *       "denormalization_context"={"groups"={"app_users_post"}}
 *     }
 *   },
 *   itemOperations={
 *     "get"={
 *       "access_control"="is_granted('ROLE_MANAGER') or object.canRead(user)",
 *       "normalization_context"={"groups"={"app_user_get"}},
 *     },
 *     "put"={
 *       "access_control"="is_granted('ROLE_ADMIN') or object.canEdit(user)",
 *       "denormalization_context"={"groups"={"app_user_put"}}
 *     },
 *     "delete"={
 *       "access_control"="is_granted('ROLE_ADMIN')",
 *       "denormalization_context"={"groups"={"app_user_delete"}}
 *     }
 *   }
 * )
 * @ApiFilter(OrderFilter::class, properties={
 *     "id": "ASC",
 *     "dateTime": "ASC",
 *     "username",
 *     "email",
 *     "givenName",
 *     "familyName",
 *     "nickname",
 *     "appUserType.name",
 *     "appUserType.shortName",
 *     "appUserType.slug"
 * })
 * @ApiFilter(ExistsFilter::class, properties={"active", "deleted"})
 * @ApiFilter(DateFilter::class, properties={"createdDateTime", "updatedDateTime", "startDateTime", "endDateTime"})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id": "exact",
 *     "appUserType": "exact",
 *     "description": "partial"
 * })
 * @Searchable({
 *     "id",
 *     "username",
 *     "email",
 *     "givenName",
 *     "additionalName",
 *     "familyName",
 *     "nickname",
 *     "honorificPrefix",
 *     "honorificSuffix",
 *     "description",
 *     "note",
 *     "appUserType.name",
 *     "appUserType.shortName",
 *     "appUserType.slug"
 * })
 * @author Jakub Zak <mail@jakubzak.eu>
 * @Doctrine\ORM\Mapping\Cache(usage="NONSTRICT_READ_WRITE", region="core_app_user")
 */
class AppUser extends AbstractAppUser
{
    /**
     * @Doctrine\ORM\Mapping\OneToMany(
     *     targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUserFlagConnection", cascade={"all"}, mappedBy="appUser", fetch="EAGER"
     * )
     */
    protected ?Collection $appUserFlags = null;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUserType", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(name="app_user_type_id", referencedColumnName="id")
     */
    protected ?AppUserType $appUserType = null;

    public function __construct(?string $fullName = null, ?string $username = null, ?string $email = null, ?string $encryptedPassword = null)
    {
        $this->appUserFlags = new ArrayCollection();
        $this->setFullName($fullName);
        $this->setUsername($username);
        $this->setEmail($email);
        $this->setPassword($encryptedPassword);
    }

    /**
     * Can user visit administration? TODO: Is used somewhere now?
     */
    public function isAdminUser(): bool
    {
        return !$this->getAppUserType() ? false : ($this->getAppUserType()->getAdminUser() ?? false);
    }

    public function getAppUserType(): ?AppUserType
    {
        return $this->appUserType;
    }

    public function setAppUserType(?AppUserType $appUserType): void
    {
        $this->appUserType = $appUserType;
    }

    /**
     * True if user is active.
     */
    public function isActive(?DateTime $referenceDateTime = null): bool
    {
        return !$this->getAccountActivationDateTime() ? false : $this->containsDateTimeInRange($referenceDateTime);
    }

    /**
     * Can user edit this user?
     */
    public function canEdit(self $user): bool
    {
        return (!($user instanceof self) || !$this->canRead($user)) ? false : $user === $this;
    }

    /**
     * Can user read this user?
     */
    public function canRead(self $user): bool
    {
        return !($user instanceof self) ? false : $user === $this;
    }

    /**
     * Returns the roles granted to the user.
     * @return array<string> The user roles.
     */
    public function getRoles(): array
    {
        return $this->getAppUserType() ? $this->getAppUserType()->getAllRoleNames()->toArray() : [];
    }

    public function addAppUserFlag(?AppUserFlagConnection $flagInJobFairUser): void
    {
        if ($flagInJobFairUser && !$this->getAppUserFlags()->contains($flagInJobFairUser)) {
            $this->getAppUserFlags()->add($flagInJobFairUser);
            $flagInJobFairUser->setAppUser($this);
        }
    }

    /**
     * @return Collection<AppUserFlag>
     */
    public function getAppUserFlags(): Collection
    {
        return $this->appUserFlags ?? new ArrayCollection();
    }

    public function removeAppUserFlag(?AppUserFlagConnection $flagInEmployer): void
    {
        if ($flagInEmployer && $this->getAppUserFlags()->removeElement($flagInEmployer)) {
            $flagInEmployer->setAppUser(null);
        }
    }

    public function getName(): string
    {
        return $this->getFullName() ?? $this->getUsername();
    }
}
