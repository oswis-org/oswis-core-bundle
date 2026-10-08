<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Attachment;

/**
 * Kdo přílohy používá (úklid nepoužitých souborů, {@see MailAttachmentCleanup}). Každý bundle s vlastními zprávami
 * (koncepty, odeslané maily) vrátí ID souborů, na které odkazuje — implementace se zaregistruje automaticky
 * (štítek `oswis.mail_attachment_usage`).
 */
interface MailAttachmentUsageProviderInterface
{
    /** @return list<int> */
    public function usedAttachmentIds(): array;
}
