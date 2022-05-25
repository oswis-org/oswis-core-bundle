<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AppUser;

use ApiPlatform\Core\Annotation\ApiResource;
use DateInterval;
use DateTime;
use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use OswisOrg\OswisCoreBundle\Enum\AppUserEditTypeEnum;
use OswisOrg\OswisCoreBundle\Interfaces\Common\BasicInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\BasicTrait;
use OswisOrg\OswisCoreBundle\Utils\StringUtils;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity]
#[Table(name: 'core_app_user_edit_request')]
#[Cache(usage: 'NONSTRICT_READ_WRITE', region: 'core_app_user')]
#[ApiResource( //
    description: 'User initiated request for change of e-mail, username or password. User is notified by e-mail with token that ', //
    collectionOperations: [
        'get'  => [
            'security'              => "is_granted('ROLE_ADMIN')",
            'normalization_context' => ["entities_get", "app_user_edit_requests_get"],
        ],
        'post' => [
            'security'                => '',
            'denormalization_context' => ["entities_get", "app_user_edit_requests_post"],
        ],
    ], itemOperations: [
    'get' => [
        'security'              => "is_granted('ROLE_ADMIN')",
        'normalization_context' => ["entity_get", "app_user_edit_request_get"],
    ],
], attributes: [
    'security' => "is_granted('ROLE_ADMIN')",
],//
)]
class AppUserEditRequest implements BasicInterface
{
    public const DEFAULT_VALID_HOURS = 24;

    use BasicTrait;

    #[Column(type: 'datetime')]
    protected ?DateTime $expireAt = null;

    #[Column(type: 'datetime')]
    protected ?DateTime $usedAt = null;

    #[Column(type: "string", enumType: AppUserEditTypeEnum::class)]
    #[NotBlank]
    protected ?AppUserEditTypeEnum $type;

    #[Column(type: 'string', length: 170, unique: true, nullable: false)]
    protected string $token = '';

    #[ManyToOne(targetEntity: AppUser::class, cascade: ['persist'], fetch: 'EAGER')]
    #[JoinColumn(name: 'app_user_id', referencedColumnName: 'id')]
    private ?AppUser $appUser = null;

    /**
     * @var string|null Identifier (e-mail or username) of edited user.
     */
    #[NotBlank]
    private ?string $userIdentifier = null;

    /**
     * @throws \Exception
     */
    public function __construct(
        ?string $userIdentifier = null,
        ?AppUserEditTypeEnum $type = null,
    ) {
        $this->userIdentifier = $userIdentifier;
        $this->type = $type;
        $this->expireAt = (new DateTime())->add(new DateInterval('PT'.self::DEFAULT_VALID_HOURS.'H'));
        $this->token = StringUtils::generateToken();
    }

    public function isValid(AppUserEditTypeEnum $type = null, ?string $userIdentifier = null): bool
    {
        return !$this->isExpired()
               && !$this->isUsed()
               && (null === $type || $type === $this->getType())
               && (null === $userIdentifier || $userIdentifier === $this->getUserIdentifier());
    }

    public function isExpired(?DateTime $dateTime = null): bool
    {
        return $this->getExpireAt() < ($dateTime ?? new DateTime());
    }

    public function getExpireAt(): ?DateTime
    {
        return $this->expireAt;
    }

    public function isUsed(): bool
    {
        return (bool)$this->usedAt;
    }

    /**
     * @return \OswisOrg\OswisCoreBundle\Enum\AppUserEditTypeEnum
     */
    public function getType(): ?AppUserEditTypeEnum
    {
        return $this->type;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->userIdentifier;
    }

    public function getUsedAt(): ?DateTime
    {
        return $this->usedAt;
    }

    public function markAsUsed(?DateTime $usedAt = null): void
    {
        $this->usedAt = $usedAt ?? new DateTime();
    }

    public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    public function setAppUser(?AppUser $appUser): void
    {
        $this->appUser = $appUser;
    }

}
