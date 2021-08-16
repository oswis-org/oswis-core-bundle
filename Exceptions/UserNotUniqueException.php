<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Exceptions;

class UserNotUniqueException extends OswisException
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? 'Identifikátor uživatele není unikátní.');
    }
}
