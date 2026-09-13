<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Validation;

/** Jeden nález kontroly mailu; zpráva česky pro člověka, který mail píše. */
final readonly class MailProblem
{
    public const string ERROR = 'error';
    public const string WARNING = 'warning';

    public function __construct(public string $severity, public string $message)
    {
    }
}
