<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Exceptions;

use InvalidArgumentException;

class PriceInvalidArgumentException extends InvalidArgumentException
{
    public function __construct(?string $message = null)
    {
        $message = ' ('.$message.')';
        parent::__construct('Chybějící nebo chybný argument pro výpočet ceny'.$message.'.');
    }
}
