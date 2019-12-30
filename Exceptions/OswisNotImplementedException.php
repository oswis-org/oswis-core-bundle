<?php

namespace Zakjakub\OswisCoreBundle\Exceptions;

class OswisNotImplementedException extends OswisException
{
    public function __construct(?string $type = null, ?string $inContext = null)
    {
        $type = $type ? ' "'.$type.'" ' : null;
        parent::__construct("Akce {$type} není {$inContext} implementována.");
    }
}
