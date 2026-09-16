<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Twig\Extension;

use OswisOrg\OswisCoreBundle\Mail\Delivery\MailFailureReason;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/** `{{ mail.statusMessage|mail_failure_reason }}` → věta pro tým, nebo `null` (viz {@see MailFailureReason}). */
final class MailFailureReasonExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [new TwigFilter('mail_failure_reason', MailFailureReason::forMessage(...))];
    }
}
