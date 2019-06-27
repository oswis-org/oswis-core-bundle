<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Exception;
use function date_create;
use function floor;

/**
 * Trait adds deleted dateTime field
 *
 */
trait DeletedTrait
{

    /**
     * Date and time.
     *
     * @var DateTime
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default" : null})
     */
    protected $deleted;

    /**
     * @param bool|null $decimal
     *
     * @return int|null
     */
    final public function getDeletedDaysAgo(?bool $decimal = false): ?int
    {
        if (!$this->deleted) {
            return null;
        }
        $ago = $this->deleted->diff(date_create());

        return $decimal ? $ago : floor($ago);
    }

    /**
     * Get date and time.
     *
     * @return DateTime
     */
    final public function getDeleted(): ?DateTime
    {
        return $this->deleted;
    }

    /**
     * Set date and time of delete.
     *
     * @param DateTime|null $deleted
     */
    final public function setDeleted(?DateTime $deleted = null): void
    {
        $this->deleted = $deleted ? date_create($deleted->getTimestamp()) : null;
    }

    /**
     * @param DateTime|null $dateTime
     *
     * @throws Exception
     */
    final public function delete(?DateTime $dateTime = null): void
    {
        $dateTime = $dateTime ?? new DateTime();
        $this->deleted = $dateTime;
    }

    /**
     * True if user is deleted (at some moment).
     *
     * @param DateTime|null $dateTime Reference date and time.
     *
     * @return bool
     */
    final public function isDeleted(?DateTime $dateTime = null): bool
    {
        if ($dateTime) {
            return $dateTime > $this->deleted;
        }

        return $this->deleted ? true : false;
    }
}
