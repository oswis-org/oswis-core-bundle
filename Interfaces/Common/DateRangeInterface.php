<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Interfaces\Common;

use DateTime;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\DateTimeRange;
use OswisOrg\OswisCoreBundle\Utils\DateTimeUtils;

interface DateRangeInterface
{
    public function isInDateRange(?DateTime $dateTime = null): bool;

    public function getStartDateTime(): ?DateTime;

    public function setStartDateTime(?DateTime $startDateTime): void;

    public function getEndDateTime(): ?DateTime;

    public function setEndDateTime(?DateTime $endDateTime): void;

    public function setStartDate(?DateTime $dateTime): void;

    public function setEndDate(?DateTime $dateTime): void;

    public function getLength(?string $type = DateTimeUtils::DATE_TIME_HOURS): ?int;

    public function getStartDate(): ?DateTime;

    public function getRangeAsText(bool $withoutYear = false): ?string;

    public function isInOnePeriod(string $period): ?bool;

    public function getEndDate(): ?DateTime;

    public function getRangeAsTextDays(?bool $withoutYear = false): ?string;

    public function getStartByFormat(string $format): ?string;

    public function getRangeAsTextMonths(?bool $withoutYear = false): ?string;

    public function getEndByFormat(string $format): ?string;

    public function getRangeAsTextYears(?bool $withoutYear = false): ?string;

    public function setDateTimeRange(?DateTimeRange $dateTimeRange = null): void;

    public function getDateTimeRange(): DateTimeRange;
}
