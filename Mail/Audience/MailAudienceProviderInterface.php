<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Audience;

/**
 * Poskytovatel údajů pro stavebnici podmínky — modul, který umí podmínku vyhodnotit (kalendář: filtr
 * přihlášek), dodá i slovník. Core sám žádné údaje nezná (směr závislostí core ← bundly).
 */
interface MailAudienceProviderInterface
{
    /** @return iterable<MailAudienceField> */
    public function getFields(): iterable;
}
