<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Entity\AppUser;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractAppUser;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Export\ExportListColumn;
use OswisOrg\OswisCoreBundle\Filter\SearchAnnotation as Searchable;
use OswisOrg\OswisCoreBundle\Interfaces\Export\PdfExportableInterface;
use OswisOrg\OswisCoreBundle\Traits\Export\PdfExportableTrait;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * User of application.
 *
 * AppUser is user of this application.
 *
 * Every **user must be activated** by activating e-mail with link that is containing special token.
 *
 * User is **active in interval** given by *startDateTime* and *endDateTime* (no need to use *deleted* property).
 *
 * @todo   Generate username from real name if username is not given.
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="OswisOrg\OswisCoreBundle\Repository\AppUserRepository")
 * @Doctrine\ORM\Mapping\Table(name="core_app_user")
 * @ApiResource(
 *   attributes={
 *     "filters"={"search"},
 *     "access_control"="is_granted('ROLE_USER')"
 *   },
 *   collectionOperations={
 *     "get"={
 *       "access_control"="is_granted('ROLE_MANAGER')",
 *       "normalization_context"={"groups"={"nameables_get", "app_users_get"}, "enable_max_depth"=true},
 *     },
 *     "pdf"={
 *       "method"="GET",
 *       "path"="/app_users/export/pdf",
 *       "access_control"="is_granted('ROLE_MANAGER')",
 *       "normalization_context"={"groups"={"nameables_get", "app_users_get"}, "enable_max_depth"=true},
 *     },
 *     "csv"={
 *       "method"="GET",
 *       "path"="/app_users/export/csv",
 *       "access_control"="is_granted('ROLE_MANAGER')",
 *       "normalization_context"={"groups"={"nameables_get", "app_users_get"}, "enable_max_depth"=true},
 *     },
 *     "post"={
 *       "access_control"="is_granted('ROLE_ADMIN')",
 *       "denormalization_context"={"groups"={"nameables_post", "app_users_post"}, "enable_max_depth"=true}
 *     }
 *   },
 *   itemOperations={
 *     "get"={
 *       "access_control"="is_granted('ROLE_MANAGER') or object.canRead(user)",
 *       "normalization_context"={"groups"={"nameable_get", "app_user_get"}, "enable_max_depth"=true},
 *     },
 *     "put"={
 *       "access_control"="is_granted('ROLE_ADMIN') or object.canEdit(user)",
 *       "denormalization_context"={"groups"={"nameable_put", "app_user_put"}, "enable_max_depth"=true}
 *     }
 *   }
 * )
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
class AppUser extends AbstractAppUser implements PdfExportableInterface
{
    use PdfExportableTrait;

    public const ENTITY_NAME = [1 => 'Uživatel', 11 => 'Uživatelé'];

    /**
     * @var string|null Temporary storage for plain text password (used in e-mails), NOT PERSISTED!
     */
    public ?string $plainPassword = null;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(
     *     targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserFlagConnection", cascade={"all"}, mappedBy="appUser", fetch="EAGER"
     * )
     * @ApiPlatform\Core\Annotation\ApiFilter(
     *     ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class,
     *     properties={"appUserFlags.id": "exact", "appUserFlags.name": "ipartial", "appUserFlags.slug": "ipartial"}
     * )
     * @ApiPlatform\Core\Annotation\ApiFilter(
     *     ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class,
     *     properties={"appUserFlags.id", "appUserFlags.name", "appUserFlags.slug"}
     * )
     */
    protected ?Collection $appUserFlags = null;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserType", fetch="EAGER", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(name="app_user_type_id", referencedColumnName="id")
     * @ApiPlatform\Core\Annotation\ApiFilter(
     *     ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class,
     *     properties={"appUserType.id": "exact", "appUserType.name": "ipartial", "appUserType.slug": "ipartial"}
     * )
     * @ApiPlatform\Core\Annotation\ApiFilter(
     *     ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class,
     *     properties={"appUserType.id", "appUserType.name", "appUserType.slug"}
     * )
     */
    protected ?AppUserType $appUserType = null;

    public function __construct(
        ?string $fullName = null,
        ?string $username = null,
        ?string $email = null,
        ?string $encryptedPassword = null,
        ?AppUserType $type = null
    ) {
        $this->appUserFlags = new ArrayCollection();
        $this->setName($fullName);
        $this->setUsername($username);
        $this->setEmail($email);
        $this->setPassword($encryptedPassword);
        $this->setAppUserType($type);
    }

    public static function getExportEntityName(int $case = 1): string
    {
        return self::ENTITY_NAME[$case];
    }

    public static function getPdfListColumns(bool $complex = false): Collection
    {
        $columns = new ArrayCollection();
        $columns->add(new ExportListColumn('id', ExportListColumn::TYPE_ID_USERNAME, 'Uživatel'));
        if (true === $complex) {
            $columns->add(new ExportListColumn('', '', '', ''));
        }

        return $columns;
    }

    /**
     * Can user visit administration? TODO: Is used somewhere now?
     */
    public function isAdminUser(): bool
    {
        return null === $this->getAppUserType() ? false : ($this->getAppUserType()->getAdminUser() ?? false);
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
        return !$this->getActivationDateTime() ? false : $this->containsDateTimeInRange($referenceDateTime);
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

    public function getName(): string
    {
        return $this->getFullName() ?? $this->getUsername();
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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword, ?UserPasswordEncoderInterface $encoder = null, bool $deletePlain = true): void
    {
        $this->plainPassword = $deletePlain ? null : $plainPassword;
        if (null !== $encoder) {
            $this->encryptPassword($plainPassword, $encoder);
        }
    }

    public function encryptPassword(?string $plainPassword, UserPasswordEncoderInterface $encoder): void
    {
        $this->setPassword($encoder->encodePassword($this, $plainPassword));
    }

}
