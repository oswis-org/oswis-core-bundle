<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\AppUserMail;

use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractMail;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractToken;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserToken;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;

/**
 * E-mail sent to some user.
 * @author Jakub Zak <mail@jakubzak.eu>
 * @OswisOrg\OswisCoreBundle\Filter\SearchAnnotation({
 *     "id",
 *     "token"
 * })
 * @ApiPlatform\Core\Annotation\ApiResource(
 *   attributes={
 *     "filters"={"search"},
 *     "security"="is_granted('ROLE_ADMIN')",
 *     "normalization_context"={"groups"={"app_user_mails_get"}, "enable_max_depth"=true},
 *     "denormalization_context"={"groups"={"app_user_mails_post"}, "enable_max_depth"=true}
 *   },
 *   collectionOperations={
 *     "get"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"app_user_mails_get"}, "enable_max_depth"=true},
 *     },
 *     "post"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "denormalization_context"={"groups"={"app_user_mails_post"}, "enable_max_depth"=true}
 *     }
 *   },
 *   itemOperations={
 *     "get"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"app_user_mail_get"}, "enable_max_depth"=true},
 *     },
 *     "put"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "denormalization_context"={"groups"={"app_user_mail_put"}, "enable_max_depth"=true}
 *     }
 *   }
 * )
 */
#[Entity]
#[Table(name: 'core_app_user_mail')]
#[Cache(usage: 'NONSTRICT_READ_WRITE', region: 'core_app_user')]
class AppUserMail extends AbstractMail
{
    public const TYPE_ACTIVATION = 'activation';
    public const TYPE_ACTIVATION_REQUEST = 'activation-request';
    public const TYPE_PASSWORD_CHANGE = 'password-change';
    public const TYPE_PASSWORD_CHANGE_REQUEST = 'password-change-request';
    public const TYPE_USER_EDIT_REQUEST = 'user-edit-request';
    public const TYPE_USER_EDIT = 'user-edit';

    #[ManyToOne(targetEntity: AppUser::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'app_user_id', referencedColumnName: 'id')]
    protected ?AppUser $appUser = null;

    #[ManyToOne(targetEntity: AppUserToken::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'app_user_token_id', referencedColumnName: 'id')]
    protected ?AppUserToken $appUserToken = null;

    /**
     * @param  AppUser  $appUser
     * @param  string  $subject
     * @param  string|null  $type
     * @param  AppUserToken|null  $appUserEditRequest
     * @param  string|null  $messageId
     *
     * @throws InvalidTypeException
     */
    public function __construct(
        AppUser $appUser = null,
        string $subject = null,
        ?string $type = null,
        AppUserToken $appUserEditRequest = null,
        ?string $messageId = null,
    ) {
        parent::__construct($subject, $appUser?->getEmail(), $type, $appUser?->getName(), $messageId);
        $this->appUser = $appUser;
        $this->appUserToken = $appUserEditRequest;
    }

    public static function getAllowedTypesDefault(): array
    {
        return [
            ...parent::getAllowedTypesDefault(),
            self::TYPE_ACTIVATION,
            self::TYPE_ACTIVATION_REQUEST,
            self::TYPE_PASSWORD_CHANGE,
            self::TYPE_PASSWORD_CHANGE_REQUEST,
            self::TYPE_USER_EDIT_REQUEST,
            self::TYPE_USER_EDIT,
        ];
    }

    public function isAppUser(?AppUser $appUser): bool
    {
        return $this->getAppUser() === $appUser;
    }

    public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    public function getAppUserToken(): ?AbstractToken
    {
        return $this->appUserToken;
    }
}
