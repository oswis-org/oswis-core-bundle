<?php

namespace Zakjakub\OswisResourcesBundle\Exceptions;

use InvalidArgumentException;

class PriceInvalidArgumentException extends InvalidArgumentException
{
    public function __construct(?string $message = null)
    {
        $message = ' ('.$message.') ';
        parent::__construct('Chybějící nebo chybný argument pro výpočet ceny'.$message.'.');
    }
}
