<?php

namespace Zakjakub\OswisCoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Gedmo\Mapping\Annotation\Timestampable;
use Zakjakub\OswisCoreBundle\Entity\AbstractClass\AbstractAppUser;
use Zakjakub\OswisCoreBundle\Filter\SearchAnnotation as Searchable;

// Dummy statement -> use not deleted as unused.
\assert(Timestampable::class);

/**
 * Class AppUser
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Zakjakub\OswisCoreBundle\Repository\AppUserRepository")
 * @Doctrine\ORM\Mapping\Table(name="app_user")
 * @ApiResource(
 *   attributes={
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
 * @ApiFilter(OrderFilter::class)
 * @ApiFilter(ExistsFilter::class, properties={"active", "deleted"})
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
 *     "note"
 * })
 */
class AppUser extends AbstractAppUser
{

    /**
     * @var AppUserType
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="Zakjakub\OswisCoreBundle\Entity\AppUserType", inversedBy="appUsers", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(name="app_user_type_id", referencedColumnName="id")
     */
    protected $appUserType;

    public function __construct(
        ?string $fullName = null,
        ?string $username = null,
        ?string $email = null,
        ?Address $address = null,
        ?\DateTime $deleted = null,
        ?string $encryptedPassword = null
    ) {
        $this->setFullName($fullName);
        $this->setUsername($username);
        $this->setEmail($email);
        $this->setFieldsFromAddress($address);
        $this->setPassword($encryptedPassword);
        $this->setDeleted($deleted);
    }

    /**
     * Can visit administration?
     * @return bool
     */
    final public function isAdminUser(): bool
    {
        if (!$this->getAppUserType()) {
            return false;
        }

        return $this->getAppUserType()->getAdminUser() ?? false;
    }

    /**
     * @return AppUserType|null
     */
    final public function getAppUserType(): ?AppUserType
    {
        return $this->appUserType ?? null;
    }

    /**
     * @param AppUserType|null $appUserType
     */
    final public function setAppUserType(?AppUserType $appUserType): void
    {
        if ($this->appUserType && $appUserType !== $this->appUserType) {
            $this->appUserType->removeAppUser($this);
        }
        if ($appUserType) {
            $appUserType->addAppUser($this);
        }
        $this->appUserType = $appUserType;
    }

    /**
     * True if user is active.
     *
     * @param \DateTime|null $referenceDateTime
     *
     * @return bool
     * @throws \Exception
     */
    final public function isActive(?\DateTime $referenceDateTime = null): bool
    {
        return $this->containsDateTimeInRange($referenceDateTime);
    }

    /**
     * Can user edit this user?
     *
     * @param $user
     *
     * @return bool
     */
    final public function canEdit(AppUser $user): bool
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
     *
     * @return bool
     */
    final public function canRead(AppUser $user): bool
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
}
