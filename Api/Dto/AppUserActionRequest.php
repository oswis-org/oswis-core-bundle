<?php

namespace Zakjakub\OswisCoreBundle\Api\Dto;

use ApiPlatform\Core\Annotation\ApiResource;
use Zakjakub\OswisCoreBundle\Entity\AppUser;

/**
 * Endpoint for actions with users (activation, password changes...).
 * @ApiResource(
 *      collectionOperations={
 *          "post"={
 *              "path"="/app_user_action",
 *          },
 *      },
 *      itemOperations={},
 * )
 */
final class AppUserActionRequest
{
    /**
     * ID of changed user.
     * ID has higher priority than username/e-mail.
     * @var int|null
     */
    public $uid;

    /**
     * Username or e-mail of changed user.
     * ID has higher priority than username/e-mail.
     * @var string|null
     */
    public $username;

    /**
     * Token generated when request was created.
     * @var string|null
     */
    public $token;

    /**
     * New password.
     * @var string|null
     */
    public $password;

    /**
     * Type of action.
     * @var string|null
     */
    public $type;

    /**
     * App user to change.
     * @var AppUser|null
     */
    public $appUser;
}
