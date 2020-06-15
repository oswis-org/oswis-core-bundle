<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\EMail;

use OswisOrg\OswisCoreBundle\Interfaces\Common\NameableInterface;

interface EMailTemplateInterface extends NameableInterface
{
    public function getTemplateString(): ?string;

    public function setTemplateString(?string $templateString): void;
}