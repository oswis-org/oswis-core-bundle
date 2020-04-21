<?php

namespace OswisOrg\OswisCoreBundle\Exceptions;

use Exception;

class PriceListNotFoundException extends Exception
{
    public function __construct(?string $message = null)
    {
        $message = ' ('.$message.') ';
        parent::__construct('Ceník nenalezen'.$message.'.');
    }
}
