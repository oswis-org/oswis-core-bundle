<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Exceptions;

class UserNotFoundException extends NotFoundException
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? 'Uživatel nenalezen.');
    }
}
