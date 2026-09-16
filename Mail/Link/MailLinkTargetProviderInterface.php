<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Link;

/**
 * Nabídka cílů odkazu z jednoho modulu (značka `oswis.mail_link_target_provider`).
 *
 * Core nezná Seznamovák: turnusy a přihlášky dodává kalendář, stránky webu web. Stejný vzor jako
 * {@see \OswisOrg\OswisCoreBundle\Mail\Catalog\MailCatalogProviderInterface}.
 */
interface MailLinkTargetProviderInterface
{
    /** @return iterable<MailLinkTarget> */
    public function getLinkTargets(): iterable;
}
