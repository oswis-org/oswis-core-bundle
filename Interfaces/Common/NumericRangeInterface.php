<?php

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use DateTime;
use Exception;

interface NumericRangeInterface
{
    public function getMin(): int;

    public function setMin(?int $min): void;

    public function getMax(): int;

    public function setMax(?int $max): void;
}
