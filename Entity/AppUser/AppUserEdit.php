<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AppUser;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
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
#[ApiResource(
    description: 'User initiated change of e-mail, username or password. User must be authenticated by token (from AppUserEditRequest).',
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['entities_get', 'app_user_edits_get']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['entities_get', 'app_user_edits_post']]
        ),
        new Get(
            normalizationContext: ['groups' => ['entity_get', 'app_user_edit_get']],
            security: "is_granted('ROLE_ADMIN')"
        ),
    ]
)]
class AppUserEdit implements BasicInterface
{
    use BasicTrait;

    #[ManyToOne(targetEntity: AppUser::class, cascade: ['persist'], fetch: 'EAGER')]
    #[JoinColumn(name: 'app_user_id', referencedColumnName: 'id')]
    protected ?AppUser $appUser = null;

    #[ManyToOne(targetEntity: AppUserEditRequest::class, cascade: ['persist'], fetch: 'EAGER')]
    #[JoinColumn(name: 'used_app_user_edit_request', referencedColumnName: 'id')]
    protected ?AppUserEditRequest $usedEditRequest = null;

    #[Column(type: "string", enumType: AppUserEditTypeEnum::class)]
    protected ?AppUserEditTypeEnum $type;

    /**
     * @var string|null Identifier (e-mail or username) of edited user.
     */
    #[NotBlank]
    protected ?string $userIdentifier;

    /**
     * @var string|null Value to be set (to property given by AppUserToken). Deleted after use.
     */
    #[NotBlank]
    protected ?string $newValue;

    /**
     * @var string|null Used value that was set to user after token/request verification.
     */
    #[Column(type: 'string', length: 170, unique: false, nullable: true)]
    protected ?string $usedValue = null;

    /**
     * @var string|null Token to be used. Deleted after use. Not persisted.
     */
    #[NotBlank]
    protected ?string $token;

    protected ?UserPasswordHasherInterface $hasher = null;

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
     * @throws TokenInvalidException
     * @throws OswisException
     */
    public function process(): void
    {
        $usedEditRequest = $this->getUsedEditRequest();
        if (!$usedEditRequest || !$usedEditRequest->isValid($this->getType(), $this->getUserIdentifier())) {
            throw new TokenInvalidException('expiroval, již byl použitý nebo má špatný typ');
        }
        $this->useValue();
        $usedEditRequest->markAsUsed();
        $this->token = null;
        $this->newValue = null;
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

    public function getType(): ?AppUserEditTypeEnum
    {
        return $this->type;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->userIdentifier;
    }

    /**
     * @throws OswisException
     */
    private function useValue(): void
    {
        match ($this->getType()) {
            AppUserEditTypeEnum::Password => $this->usePassword(),
            AppUserEditTypeEnum::Username => $this->useUsername(),
            AppUserEditTypeEnum::EMail => $this->useEMail(),
            default => null,
        };
    }

    /**
     * @throws OswisException
     */
    protected function usePassword(): void
    {
        if (!$this->hasher || empty($this->newValue)) {
            throw new OswisException('Heslo nelze zakódovat.');
        }
        $this->getAppUser()?->setPassword($this->hasher->hashPassword($this->getAppUser(), $this->newValue));
    }

    public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    protected function useUsername(): void
    {
        $this->usedValue = $this->newValue;
        $this->getAppUser()?->setUsername($this->newValue);
    }

    protected function useEMail(): void
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
