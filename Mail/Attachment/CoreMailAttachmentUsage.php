<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Attachment;

use Doctrine\DBAL\Connection;

/** Přílohy odeslané maily z core (systémové a účtové) — záznam „co odešlo navíc". */
final readonly class CoreMailAttachmentUsage implements MailAttachmentUsageProviderInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function usedAttachmentIds(): array
    {
        return MailAttachmentIds::fromTables($this->connection, ['core_system_mail', 'core_app_user_mail', 'core_app_user_edit_mail']);
    }
}
