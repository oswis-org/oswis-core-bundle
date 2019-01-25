<?php

namespace Zakjakub\OswisCoreBundle\Interfaces;

interface BasicMailConfirmationInterface
{
    public function getMailConfirmationDateTime(): ?\DateTime;

    public function setMailConfirmationSend(?string $source): void;
}
