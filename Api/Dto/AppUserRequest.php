<?php

namespace Zakjakub\OswisCoreBundle\Api\Dto;

use ApiPlatform\Core\Annotation\ApiResource;
use Zakjakub\OswisCoreBundle\Entity\AppUser;

/**
 * @ApiResource(
 *      collectionOperations={
 *          "post"={
 *              "path"="/app_user_action",
 *          },
 *      },
 *      itemOperations={},
 * )
 */
final class AppUserRequest
{
    /**
     * ID of AppUser which is changed.
     * @var int|null
     */
    public $uid;

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
