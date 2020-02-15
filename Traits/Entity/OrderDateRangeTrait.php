<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Exception;
use Zakjakub\OswisCoreBundle\Utils\DateTimeUtils;

trait OrderDateRangeTrait
{
    /**
     * Date and time of range start.
     *
     * @var DateTime|null
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected ?DateTime $startOrderDateTime = null;

    /**
     * Date and time of range end.
     *
     * @var DateTime|null
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected ?DateTime $endOrderDateTime = null;

    public function getStartOrderDateTime(): ?DateTime
    {
        return $this->startOrderDateTime;
    }

    public function setStartOrderDateTime(?DateTime $startOrderDateTime): void
    {
        $this->startOrderDateTime = $startOrderDateTime;
    }

    /**
     * True if datetime belongs to this datetime range.
     *
     * @param DateTime|null $dateTime Checked date and time
     *
     * @return bool True if belongs to date range
     * @throws Exception
     */
    public function containsOrderDateTime(DateTime $dateTime): bool
    {
        return DateTimeUtils::isDateTimeInRange($this->startOrderDateTime, $this->getEndOrderDateTime(), $dateTime);
    }

    public function getEndOrderDateTime(): ?DateTime
    {
        return $this->endOrderDateTime;
    }

    public function setEndOrderDateTime(?DateTime $endOrderDateTime): void
    {
        $this->endOrderDateTime = $endOrderDateTime;
    }
}
