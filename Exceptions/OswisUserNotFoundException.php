<?php

namespace Zakjakub\OswisCoreBundle\Exceptions;

class OswisUserNotFoundException extends OswisException
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? 'Uživatel nenalezen.');
    }
}
