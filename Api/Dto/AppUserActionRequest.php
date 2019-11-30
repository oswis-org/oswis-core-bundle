<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Api\Dto;

use ApiPlatform\Core\Annotation\ApiResource;
use Zakjakub\OswisCoreBundle\Entity\AppUser;
use Zakjakub\OswisCoreBundle\Manager\AppUserManager;

/**
 * Endpoint for actions with users (activation, password changes...).
 *
 * @ApiResource(
 *      collectionOperations={
 *          "post"={
 *              "path"="/app_user_action",
 *          },
 *      },
 *      itemOperations={},
 *      output=false
 * )
 */
final class AppUserActionRequest
{
    public const ALLOWED_TYPES = AppUserManager::ALLOWED_TYPES;

    /**
     * ID of changed user.
     * ID has higher priority than username/e-mail.
     *
     * @var int|null
     */
    public ?int $uid = null;

    /**
     * Username or e-mail of changed user.
     * ID has higher priority than username/e-mail.
     *
     * @var string|null
     */
    public ?string $username = null;

    /**
     * Token generated when request was created.
     *
     * @var string|null
     */
    public ?string $token = null;

    /**
     * New password.
     *
     * @var string|null
     */
    public ?string $password = null;

    /**
     * Type of action.
     *
     * @var string|null
     * @Symfony\Component\Validator\Constraints\Choice(
     *     choices=AppUserActionRequest::ALLOWED_TYPES,
     *     message="Požadovaný typ akce není implementovaný."
     * )
     */
    public ?string $type = null;

    /**
     * App user to change.
     *
     * @var AppUser|null
     */
    public ?AppUser $appUser = null;
}
