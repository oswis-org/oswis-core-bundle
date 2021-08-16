<?php

declare(strict_types=1);

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
