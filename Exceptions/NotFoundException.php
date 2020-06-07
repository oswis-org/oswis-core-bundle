<?php

namespace OswisOrg\OswisCoreBundle\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFoundException extends NotFoundHttpException
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? 'Stránka nenalezena.');
    }
}
