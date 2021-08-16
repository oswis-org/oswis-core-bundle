<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Exceptions;

class NotImplementedException extends OswisException
{
    public function __construct(?string $type = null, ?string $inContext = null)
    {
        parent::__construct("Akce $type není $inContext implementována.");
    }
}
