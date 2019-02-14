<?php

namespace Zakjakub\OswisCoreBundle\Api\Dto;

use ApiPlatform\Core\Annotation\ApiResource;

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
     * @var int|null
     */
    public $uid;

    /**
     * @var string|null
     */
    public $token;

    /**
     * @var string|null
     */
    public $password;

    /**
     * @var string|null
     */
    public $type;
}
