<?php

namespace OswisOrg\OswisCoreBundle\Exceptions;

class OswisNotImplementedException extends OswisException
{
    public function __construct(?string $type = null, ?string $inContext = null)
    {
        parent::__construct("Akce {$type} není {$inContext} implementována.");
    }
}
