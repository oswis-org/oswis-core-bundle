<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

interface TextValueInterface
{
    public function getTextValue(): ?string;

    public function setTextValue(?string $textValue): void;

    public function hasTextValue(): bool;
}
