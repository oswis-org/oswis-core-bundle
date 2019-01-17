<?php

namespace Zakjakub\OswisCoreBundle\Exceptions;

use Exception;

class BirthDateMissingException extends Exception
{
    public function __construct()
    {
        parent::__construct('Není zadán věk.');
    }
}
