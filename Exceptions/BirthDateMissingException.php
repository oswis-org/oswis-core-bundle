<?php

namespace Zakjakub\OswisResourcesBundle\Exceptions;

use Exception;

class BirthDateMissingException extends Exception
{
    public function __construct()
    {
        parent::__construct('Není zadán věk.');
    }
}
