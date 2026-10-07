<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Attachment;

/** Soubor, který {@see \OswisOrg\OswisCoreBundle\Service\MailService::sendEMail()} přiloží ke zprávě. */
final readonly class AttachedFile
{
    public function __construct(
        public string $path,
        public string $name,
        public string $mime,
    ) {
    }
}
