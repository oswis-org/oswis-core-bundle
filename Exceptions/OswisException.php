<?php

namespace Zakjakub\OswisCoreBundle\Exceptions;

use Exception;

class OswisException extends Exception
{
    public function __construct(?string $message = null)
    {
        $message = ' ('.$message.') ';
        parent::__construct('[OSWIS][ERROR] '.$message.'.');
    }
}
