<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Exceptions;

use InvalidArgumentException;

class InvalidTypeException extends InvalidArgumentException
{
    public function __construct(?string $type = null, ?string $context = null)
    {
        parent::__construct("Typ $type není $context povolen");
    }
}
