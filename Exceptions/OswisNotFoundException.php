<?php

namespace Zakjakub\OswisCoreBundle\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OswisNotFoundException extends NotFoundHttpException
{
    public function __construct(?string $message = null)
    {
        $message = $message ? ' '.$message : null;
        parent::__construct('Stránka nenalezena.'.$message);
    }
}
