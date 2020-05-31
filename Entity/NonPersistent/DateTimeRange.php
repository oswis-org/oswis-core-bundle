<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

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
