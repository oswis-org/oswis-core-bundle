<?php

namespace OswisOrg\OswisCoreBundle\Entity\Security;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

#[Entity()]
#[Table(name: 'refresh_tokens')]
class RefreshToken extends BaseRefreshToken
{

}