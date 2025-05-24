<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\AppUser;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractAppUser;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Export\ExportListColumn;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;
use OswisOrg\OswisCoreBundle\Interfaces\Export\PdfExportableInterface;
use OswisOrg\OswisCoreBundle\Repository\AppUserRepository;
use OswisOrg\OswisCoreBundle\Traits\Export\PdfExportableTrait;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

/**
 * User of application.
 * AppUser is user of this application.
 * Every **user must be activated** by activating e-mail with link that is containing special token.
 * User is **active in interval** given by *startDateTime* and *endDateTime* (no need to use *deleted* property).
 * @author Jakub Zak <mail@jakubzak.eu>
 * @OswisOrg\OswisCoreBundle\Filter\SearchAnnotation({
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
 */
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => ["entities_get", "app_users_get"],
                'enable_max_depth' => true,
            ],
            security: "is_granted('ROLE_MANAGER')"
        ),
        new Post(
            denormalizationContext: [
                'groups' => ["entities_post", "app_users_post"],
                'enable_max_depth' => true,
            ],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Get(
            normalizationContext: [
                'groups' => ["entity_get", "app_user_get"],
                'enable_max_depth' => true,
            ],
            security: "is_granted('ROLE_MANAGER') or object.canRead(user)"
        ),
        new Put(
            denormalizationContext: [
                'groups' => ["entity_put", "app_user_put"],
                'enable_max_depth' => true,
            ],
            security: "is_granted('ROLE_ADMIN') or object.canEdit(user)"
        ),
        new Get(
            uriTemplate: '/app_users/export/pdf',
            normalizationContext: [
                'groups' => ["entities_get", "app_users_get"],
                'enable_max_depth' => true,
            ],
            security: "is_granted('ROLE_MANAGER')"
        ),
    ],
    filters: ['search'],
    security: "is_granted('ROLE_USER')"
)]
#[ApiFilter(OrderFilter::class, properties: [
    "appUserType.id",
    "appUserType.name",
    "appUserType.slug",
])]
#[ORM\Entity(repositoryClass: AppUserRepository::class)]
#[ORM\Table(name: 'core_app_user')]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'core_app_user')]
class AppUser extends AbstractAppUser implements PdfExportableInterface, PasswordAuthenticatedUserInterface
{
    use PdfExportableTrait;

    public const ENTITY_NAME = [1 => 'Uživatel', 11 => 'Uživatelé'];

    #[ORM\OneToMany(targetEntity: AppUserFlagConnection::class, mappedBy: 'appUser', cascade: ['all'], fetch: 'EAGER')]
    #[ApiFilter(SearchFilter::class, properties: [
        "appUserFlags.id"   => "exact",
        "appUserFlags.name" => "ipartial",
        "appUserFlags.slug" => "ipartial",
    ])]
    #[ApiFilter(OrderFilter::class, properties: ["appUserFlags.id", "appUserFlags.name", "appUserFlags.slug"])]
    protected ?Collection $appUserFlags = null;

    #[ORM\ManyToOne(targetEntity: AppUserType::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'app_user_type_id', referencedColumnName: 'id')]
    #[ApiFilter(SearchFilter::class, properties: [
        "appUserType.id"   => "exact",
        "appUserType.name" => "ipartial",
        "appUserType.slug" => "ipartial",
    ])]
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
     * Can user visit administration?
     */
    public function isAdminUser(): bool
    {
        return (null !== $this->getAppUserType()) && ($this->getAppUserType()->getAdminUser());
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
     * Can user edit this user?
     */
    public function canEdit(self $user): bool
    {
        return $this->canRead($user) && $user === $this;
    }

    /**
     * Can user read this user?
     */
    public function canRead(self $user): bool
    {
        return $user === $this;
    }

    /**
     * Returns the roles granted to the user.
     * @return array<string>|array The user roles.
     */
    public function getRoles(): array
    {
        return $this->getAppUserType()?->getAllRoleNames()->toArray() ?? [];
    }

    public function getName(): string
    {
        return (!empty($fullName = $this->getFullName()) ? $fullName : $this->getUsername()) ?? '';
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

    /**
     * @param string|null                 $plainPassword
     * @param UserPasswordHasherInterface $encoder
     */
    public function encryptPassword(?string $plainPassword, UserPasswordHasherInterface $encoder): void
    {
        $this->setPassword($encoder->hashPassword($this, ''.$plainPassword));
    }

    /**
     * @return non-empty-string
     */
    public function getUserIdentifier(): string
    {
        return !empty($this->getUsername()) ? $this->getUsername() : $this->getEmail();
    }
}

