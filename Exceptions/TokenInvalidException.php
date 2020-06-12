<?php

namespace OswisOrg\OswisCoreBundle\Exceptions;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TokenInvalidException extends AccessDeniedException
{
    public function __construct(string $message, ?string $token = null)
    {
        parent::__construct("Token $token není platný ($message).");
    }
}
