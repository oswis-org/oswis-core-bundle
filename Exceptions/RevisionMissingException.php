<?php

namespace OswisOrg\OswisCoreBundle\Exceptions;

use Exception;

class RevisionMissingException extends Exception
{
    public function __construct(?string $message = null)
    {
        $message = ' ('.$message.') ';
        parent::__construct('Verze položky nenalezena'.$message.'.');
    }
}
