<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\Mail;

use OswisOrg\OswisCoreBundle\Interfaces\Common\NameableInterface;

interface MailTemplateInterface extends NameableInterface
{
    public function getTemplateString(): ?string;

    public function setTemplateString(?string $templateString): void;
}