<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use DateTimeInterface;
use Exception;
use Zakjakub\OswisCoreBundle\Utils\DateTimeUtils;

trait OrderDateRangeTrait
{
    /**
     * Date and time of range start.
     *
     * @var DateTimeInterface|null
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected ?DateTimeInterface $startOrderDateTime = null;

    /**
     * Date and time of range end.
     *
     * @var DateTimeInterface|null
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default": null})
     */
    protected ?DateTimeInterface $endOrderDateTime = null;

    public function getStartOrderDateTime(): ?DateTimeInterface
    {
        return $this->startOrderDateTime;
    }

    public function setStartOrderDateTime(?DateTimeInterface $startOrderDateTime): void
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
    public function containsOrderDateTime(DateTimeInterface $dateTime): bool
    {
        return DateTimeUtils::isDateTimeInRange($this->startOrderDateTime, $this->getEndOrderDateTime(), $dateTime);
    }

    public function getEndOrderDateTime(): ?DateTimeInterface
    {
        return $this->endOrderDateTime;
    }

    public function setEndOrderDateTime(?DateTimeInterface $endOrderDateTime): void
    {
        $this->endOrderDateTime = $endOrderDateTime;
    }
}
