<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Exceptions;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TokenInvalidException extends AccessDeniedException
{
    public function __construct(string $message, ?string $token = null)
    {
        parent::__construct("Token $token není platný ($message).");
    }
}
