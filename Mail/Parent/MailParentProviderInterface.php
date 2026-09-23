<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Parent;

/**
 * Nabídka obálek z jednoho modulu (značka `oswis.mail_parent_provider`).
 *
 * Core nezná přihlášky — obálky k nim dodává kalendář. Stejný vzor jako
 * {@see \OswisOrg\OswisCoreBundle\Mail\Link\MailLinkTargetProviderInterface}.
 */
interface MailParentProviderInterface
{
    /** @return iterable<MailParent> */
    public function getParents(): iterable;
}
