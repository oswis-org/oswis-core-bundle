<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\AppUser;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractToken;
use OswisOrg\OswisCoreBundle\Exceptions\TokenInvalidException;
use OswisOrg\OswisCoreBundle\Filter\SearchAnnotation as Searchable;
use OswisOrg\OswisCoreBundle\Repository\AppUserTokenRepository;

/**
 * @ApiResource(
 *   attributes={
 *     "filters"={"search"},
 *     "security"="is_granted('ROLE_ADMIN')",
 *     "normalization_context"={"groups"={"app_user_roles_get"}, "enable_max_depth"=true},
 *     "denormalization_context"={"groups"={"app_user_roles_post"}, "enable_max_depth"=true}
 *   },
 *   collectionOperations={
 *     "get"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"app_user_roles_get"}, "enable_max_depth"=true},
 *     },
 *     "post"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "denormalization_context"={"groups"={"app_user_roles_post"}, "enable_max_depth"=true}
 *     }
 *   },
 *   itemOperations={
 *     "get"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"app_user_role_get"}, "enable_max_depth"=true},
 *     },
 *     "put"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "denormalization_context"={"groups"={"app_user_role_put"}, "enable_max_depth"=true}
 *     }
 *   }
 * )
 * @Searchable({
 *     "id",
 *     "token"
 * })
 * @author Jakub Zak <mail@jakubzak.eu>
 */
#[Entity(repositoryClass: AppUserTokenRepository::class)]
#[Table(name: 'core_app_user_token')]
#[Cache(usage: 'NONSTRICT_READ_WRITE', region: 'core_app_user')]
class AppUserToken extends AbstractToken
{
    #[ManyToOne(targetEntity: AppUser::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'app_user_id', referencedColumnName: 'id')]
    protected ?AppUser $appUser = null;

    public function __construct(
        ?AppUser $appUser = null,
        ?string $eMail = null,
        ?string $type = null,
        bool $multipleUseAllowed = false,
        ?int $validHours = null,
        ?int $level = null
    ) {
        parent::__construct($eMail, $type, $multipleUseAllowed, $validHours, $level);
        $this->appUser = $appUser;
    }

    public function isAppUser(AppUser $appUser): bool
    {
        try {
            return $this->getAppUser() === $appUser;
        } catch (TokenInvalidException) {
            return false;
        }
    }

    /**
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\TokenInvalidException
     */
    public function getAppUser(): AppUser
    {
        return $this->appUser ?? throw new TokenInvalidException('Token není přiřazen žádnému uživateli.');
    }
}
