<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

use DateTime;

class DateTimeRange
{
    public ?DateTime $startDateTime = null;

    public ?DateTime $endDateTime = null;

    public function __construct(?DateTime $startDateTime = null, ?DateTime $endDateTime = null)
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;
    }
}
