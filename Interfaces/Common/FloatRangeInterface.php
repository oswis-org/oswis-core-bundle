<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

interface FloatRangeInterface
{
    public function getMin(): ?float;

    public function setMin(?float $min): void;

    public function getMax(): ?float;

    public function setMax(?float $max): void;
}
