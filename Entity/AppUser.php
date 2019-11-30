<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Zakjakub\OswisCoreBundle\Entity\AbstractClass\AbstractAppUser;
use Zakjakub\OswisCoreBundle\Filter\SearchAnnotation as Searchable;

/**
 * User of application.
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Zakjakub\OswisCoreBundle\Repository\AppUserRepository")
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
 *     "additionalName",
 *     "familyName",
 *     "nickname",
 *     "honorificPrefix",
 *     "honorificSuffix",
 *     "description",
 *     "note",
 *     "appUserType.name",
 *     "appUserType.shortName",
 *     "appUserType.slug",
 *     "city"
 * })
 * @ApiFilter(ExistsFilter::class, properties={"active", "deleted"})
 * @ApiFilter(DateFilter::class, properties={"createdDateTime", "updatedDateTime", "startDateTime", "endDateTime"})
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
 *     "appUserType.slug",
 *     "city"
 * })
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 * @Doctrine\ORM\Mapping\Cache(usage="NONSTRICT_READ_WRITE", region="core_app_user")
 */
class AppUser extends AbstractAppUser
{
    /**
     * @var Collection|null
     * @Doctrine\ORM\Mapping\OneToMany(
     *     targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUserFlagConnection",
     *     cascade={"all"},
     *     mappedBy="appUser",
     *     fetch="EAGER"
     * )
     */
    protected ?Collection $appUserFlags = null;

    /**
     * @var AppUserType|null
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUserType", inversedBy="appUsers", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(name="app_user_type_id", referencedColumnName="id")
     */
    protected ?AppUserType $appUserType = null;

    public function __construct(
        ?string $fullName = null,
        ?string $username = null,
        ?string $email = null,
        ?Address $address = null,
        ?DateTime $deleted = null,
        ?string $encryptedPassword = null
    ) {
        $this->setFullName($fullName);
        $this->setUsername($username);
        $this->setEmail($email);
        $this->setFieldsFromAddress($address);
        $this->setPassword($encryptedPassword);
        $this->setDeleted($deleted);
        $this->appUserFlags = new ArrayCollection();
    }

    /**
     * Can visit administration?
     */
    final public function isAdminUser(): bool
    {
        if (!$this->getAppUserType()) {
            return false;
        }

        return $this->getAppUserType()->getAdminUser() ?? false;
    }

    final public function getAppUserType(): ?AppUserType
    {
        return $this->appUserType;
    }

    final public function setAppUserType(?AppUserType $appUserType): void
    {
        if ($this->appUserType && $appUserType !== $this->appUserType) {
            $this->appUserType->removeAppUser($this);
        }
        $this->appUserType = $appUserType;
        if ($appUserType && $this->appUserType !== $appUserType) {
            $appUserType->addAppUser($this);
        }
    }

    /**
     * True if user is active.
     */
    final public function isActive(?DateTime $referenceDateTime = null): bool
    {
        if (!$this->getAccountActivationDateTime()) {
            return false;
        }

        return $this->containsDateTimeInRange($referenceDateTime);
    }

    /**
     * Can user edit this user?
     *
     * @param $user
     */
    final public function canEdit(self $user): bool
    {
        if (!($user instanceof self) || !$this->canRead($user)) {
            return false;
        }

        return $user === $this;
    }

    /**
     * Can user read this user?
     *
     * @param $user
     */
    final public function canRead(self $user): bool
    {
        if (!($user instanceof self)) { // User is not logged in.
            return false;
        }
        if ($user === $this) {
            return true;
        }

        return false;
    }

    /**
     * Returns the roles granted to the user.
     *
     * @return array (Role|string)[] The user roles
     */
    final public function getRoles(): array
    {
        return $this->getAppUserType() ? $this->getAppUserType()->getAllRoleNames()->toArray() : [];
    }

    final public function getAppUserFlags(): Collection
    {
        return $this->appUserFlags ?? new ArrayCollection();
    }

    final public function addAppUserFlag(?AppUserFlagConnection $flagInJobFairUser): void
    {
        if ($flagInJobFairUser && !$this->appUserFlags->contains($flagInJobFairUser)) {
            $this->appUserFlags->add($flagInJobFairUser);
            $flagInJobFairUser->setAppUser($this);
        }
    }

    final public function removeAppUserFlag(?AppUserFlagConnection $flagInEmployer): void
    {
        if (!$flagInEmployer) {
            return;
        }
        if ($this->appUserFlags->removeElement($flagInEmployer)) {
            $flagInEmployer->setAppUser(null);
        }
    }
}
