<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use DateTime;

interface BasicMailConfirmationInterface
{
    public function getMailConfirmationDateTime(): ?DateTime;

    public function setMailConfirmationSend(?string $source): void;
}
