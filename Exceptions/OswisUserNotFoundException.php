<?php

namespace Zakjakub\OswisCoreBundle\Exceptions;

use Exception;

class OswisUserNotFoundException extends Exception
{
    public function __construct(?string $message = null)
    {
        $message = $message ? ' '.$message : null;
        parent::__construct('Uživatel nenalezen.'.$message);
    }
}
