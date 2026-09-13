<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Catalog;

/** Poskytovatel položek nabídky (tag `oswis.mail_catalog_provider`). */
interface MailCatalogProviderInterface
{
    /** @return iterable<MailCatalogItem> */
    public function getItems(): iterable;
}
