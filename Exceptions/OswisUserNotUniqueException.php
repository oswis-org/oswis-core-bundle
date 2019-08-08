<?php

namespace Zakjakub\OswisCoreBundle\Exceptions;

use Exception;

class OswisUserNotUniqueException extends Exception
{
    public function __construct(?string $message = null)
    {
        $message = ' '.$message;
        parent::__construct('Chyba! Nejednoznačná identifikace uživatele.'.$message);
    }
}
