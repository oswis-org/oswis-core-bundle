<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AppUser;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use OswisOrg\OswisCoreBundle\Enum\AppUserEditTypeEnum;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Exceptions\TokenInvalidException;
use OswisOrg\OswisCoreBundle\Interfaces\Common\BasicInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\BasicTrait;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity]
#[Table(name: 'core_app_user_edit')]
#[Cache(usage: 'NONSTRICT_READ_WRITE', region: 'core_app_user')]
#[ApiResource( //
    description: 'User initiated change of e-mail, username or password. User must be authenticated by token (from AppUserEditRequest).', //
    collectionOperations: [
        'get'  => [
            'security'              => "is_granted('ROLE_ADMIN')",
            'normalization_context' => ["entities_get", "app_user_edits_get"],
        ],
        'post' => [
            'denormalization_context' => ["entities_get", "app_user_edits_post"],
        ],
    ], itemOperations: [
    'get' => [
        'security'              => "is_granted('ROLE_ADMIN')",
        'normalization_context' => ["entity_get", "app_user_edit_get"],
    ],
])]
class AppUserEdit implements BasicInterface
{
    use BasicTrait;

    #[ManyToOne(targetEntity: AppUser::class, cascade: ['persist'], fetch: 'EAGER')]
    #[JoinColumn(name: 'app_user_id', referencedColumnName: 'id')]
    private ?AppUser $appUser = null;

    #[ManyToOne(targetEntity: AppUserEditRequest::class, cascade: ['persist'], fetch: 'EAGER')]
    #[JoinColumn(name: 'used_app_user_edit_request', referencedColumnName: 'id')]
    private ?AppUserEditRequest $usedEditRequest = null;

    #[Column(type: "string", enumType: AppUserEditTypeEnum::class)]
    private ?AppUserEditTypeEnum $type;

    /**
     * @var string|null Identifier (e-mail or username) of edited user.
     */
    #[NotBlank]
    private ?string $userIdentifier;

    /**
     * @var string|null Value to be set (to property given by AppUserToken). Deleted after use.
     */
    #[NotBlank]
    private ?string $newValue;

    /**
     * @var string|null Used value that was set to user after token/request verification.
     */
    #[Column(type: 'string', length: 170, unique: false, nullable: true)]
    private ?string $usedValue = null;

    /**
     * @var string|null Token to be used. Deleted after use. Not persisted.
     */
    #[NotBlank]
    private ?string $token;

    private ?UserPasswordHasherInterface $hasher = null;

    public function __construct(
        ?AppUserEditTypeEnum $type = null,
        ?string $token = null,
        ?string $newValue = null,
        ?string $userIdentifier = null,
    ) {
        $this->type = $type;
        $this->token = $token;
        $this->newValue = $newValue;
        $this->userIdentifier = $userIdentifier;
    }

    /**
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\TokenInvalidException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\OswisException
     */
    public function process(): void
    {
        $usedEditRequest = $this->getUsedEditRequest();
        if (!$usedEditRequest || !$usedEditRequest->isValid($this->getType(), $this->getUserIdentifier())) {
            throw new TokenInvalidException('expiroval, již byl použitý nebo má špatný typ');
        }
        $this->useValue();
        $this->token = null;
        $usedEditRequest->markAsUsed();
    }

    public function getUsedEditRequest(): ?AppUserEditRequest
    {
        return $this->usedEditRequest;
    }

    public function setUsedEditRequest(?AppUserEditRequest $usedEditRequest): void
    {
        $this->usedEditRequest = $usedEditRequest;
        $this->appUser = $usedEditRequest?->getAppUser();
    }

    public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    public function getType(): ?AppUserEditTypeEnum
    {
        return $this->type;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->userIdentifier;
    }

    /**
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\OswisException
     */
    private function useValue(): void
    {
        match ($this->getType()) {
            AppUserEditTypeEnum::Password => $this->usePassword(),
            AppUserEditTypeEnum::Username => $this->useUsername(),
            AppUserEditTypeEnum::EMail => $this->useEMail(),
            default => null,
        };
        $this->newValue = null;
    }

    /**
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\OswisException
     */
    private function usePassword(): void
    {
        if (!$this->hasher || empty($this->newValue)) {
            throw new OswisException('Heslo nelze zakódovat.');
        }
        $this->getAppUser()?->setPassword($this->hasher->hashPassword($this->getAppUser(), $this->newValue));
    }

    private function useUsername(): void
    {
        $this->usedValue = $this->newValue;
        $this->getAppUser()?->setUsername($this->newValue);
    }

    private function useEMail(): void
    {
        $this->usedValue = $this->newValue;
        $this->getAppUser()?->setEmail($this->newValue);
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getNewValue(): ?string
    {
        return $this->newValue;
    }

    public function getUsedValue(): ?string
    {
        return $this->usedValue;
    }

    public function setHasher(?UserPasswordHasherInterface $hasher): void
    {
        $this->hasher = $hasher;
    }

}