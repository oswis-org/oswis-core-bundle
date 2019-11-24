<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Interfaces;

use DateTime;

interface BasicMailConfirmationInterface
{
    public function getMailConfirmationDateTime(): ?DateTime;

    public function setMailConfirmationSend(?string $source): void;
}
