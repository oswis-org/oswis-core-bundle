<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use DateTime;
use Exception;

interface FloatRangeInterface
{
    public function getMin(): float;

    public function setMin(?float $min): void;

    public function getMaxAge(): float;

    public function setMaxAge(?float $maxAge): void;
}
