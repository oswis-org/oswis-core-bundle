<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

interface FloatValueInterface
{
    public function getFloatValue(): ?float;

    public function setFloatValue(?float $min): void;
}
