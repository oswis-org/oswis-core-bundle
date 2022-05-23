<?php

namespace OswisOrg\OswisCoreBundle\Entity\Security;

use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

/**
 * @Doctrine\ORM\Mapping\Entity()
 * @Doctrine\ORM\Mapping\Table(name="refresh_tokens")
 */
class RefreshToken extends BaseRefreshToken
{

}