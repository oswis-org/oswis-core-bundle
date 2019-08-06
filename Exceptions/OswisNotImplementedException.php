<?php

namespace Zakjakub\OswisCoreBundle\Exceptions;

use Exception;

class OswisNotImplementedException extends Exception
{
    public function __construct(?string $message = null)
    {
        $message = ' ('.$message.') ';
        parent::__construct('Akce není implementována. '.$message.'.');
    }
}
