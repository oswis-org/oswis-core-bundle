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
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEdit;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEditRequest;

/**
 * E-mail sent to some user about changes in account (password, e-mail or username).
 * @author Jakub Zak <mail@jakubzak.eu>
 * @OswisOrg\OswisCoreBundle\Filter\SearchAnnotation({
 *     "id",
 *     "token"
 * })
 * @ApiPlatform\Core\Annotation\ApiResource(
 *   attributes={
 *     "filters"={"search"},
 *     "security"="is_granted('ROLE_ADMIN')",
 *     "normalization_context"={"groups"={"app_user_edit_mails_get"}, "enable_max_depth"=true},
 *     "denormalization_context"={"groups"={"app_user_edit_mails_post"}, "enable_max_depth"=true}
 *   },
 *   collectionOperations={
 *     "get"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"app_user_edit_mails_get"}, "enable_max_depth"=true},
 *     },
 *     "post"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "denormalization_context"={"groups"={"app_user_edit_mails_post"}, "enable_max_depth"=true}
 *     }
 *   },
 *   itemOperations={
 *     "get"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"app_user_edit_mail_get"}, "enable_max_depth"=true},
 *     },
 *     "put"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "denormalization_context"={"groups"={"app_user_edit_mail_put"}, "enable_max_depth"=true}
 *     }
 *   }
 * )
 */
#[Entity]
#[Table(name: 'core_app_user_edit_mail')]
#[Cache(usage: 'NONSTRICT_READ_WRITE', region: 'core_app_user')]
class AppUserEditMail extends AbstractMail
{
    public const TYPE_USER_EDIT_REQUEST = 'user-edit-request';
    public const TYPE_USER_EDIT = 'user-edit';

    #[ManyToOne(targetEntity: AppUser::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'app_user_id', referencedColumnName: 'id')]
    protected ?AppUser $appUser = null;

    #[ManyToOne(targetEntity: AppUserEditRequest::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'app_user_edit_request_id', referencedColumnName: 'id')]
    protected ?AppUserEditRequest $appUserEditRequest = null;

    #[ManyToOne(targetEntity: AppUserEdit::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'app_user_edit_id', referencedColumnName: 'id')]
    protected ?AppUserEdit $appUserEdit = null;

    /**
     * @param  string|null  $subject
     * @param  string|null  $type
     * @param  \OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEditRequest|null  $appUserEditRequest
     * @param  \OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEdit|null  $appUserEdit
     * @param  string|null  $messageId
     *
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException
     */
    public function __construct(
        string $subject = null,
        ?string $type = null,
        ?AppUserEditRequest $appUserEditRequest = null,
        ?AppUserEdit $appUserEdit = null,
        ?string $messageId = null,
    ) {
        $this->appUser = $appUser = $appUserEditRequest?->getAppUser();
        parent::__construct($subject, $appUser?->getEmail(), $type, $appUser?->getName(), $messageId);
        $this->appUser = $appUser;
        $this->appUserEditRequest = $appUserEditRequest;
        $this->appUserEdit = $appUserEdit;
    }

    public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    public static function getAllowedTypesDefault(): array
    {
        return [
            ...parent::getAllowedTypesDefault(),
            self::TYPE_USER_EDIT_REQUEST,
            self::TYPE_USER_EDIT,
        ];
    }

    public function isAppUser(?AppUser $appUser): bool
    {
        return $this->getAppUser() === $appUser;
    }

    public function getAppUserEditRequest(): ?AppUserEditRequest
    {
        return $this->appUserEditRequest;
    }

    public function getAppUserEdit(): ?AppUserEdit
    {
        return $this->appUserEdit;
    }
}
