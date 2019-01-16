<?php

namespace Zakjakub\OswisResourcesBundle\Api\Dto;

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
    public $uid;

    public $password;

    public $type;
}
