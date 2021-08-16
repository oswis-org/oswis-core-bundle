<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\AppUser;

use ApiPlatform\Core\Annotation\ApiResource;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractToken;
use OswisOrg\OswisCoreBundle\Exceptions\TokenInvalidException;
use OswisOrg\OswisCoreBundle\Filter\SearchAnnotation as Searchable;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="OswisOrg\OswisCoreBundle\Repository\AppUserTokenRepository")
 * @Doctrine\ORM\Mapping\Table(name="core_app_user_token")
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
 * @Doctrine\ORM\Mapping\Cache(usage="NONSTRICT_READ_WRITE", region="core_app_user")
 */
class AppUserToken extends AbstractToken
{
    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(name="app_user_id", referencedColumnName="id")
     */
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
