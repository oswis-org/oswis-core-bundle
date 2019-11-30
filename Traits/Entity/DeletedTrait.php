<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Exception;
use function date_create;
use function floor;

/**
 * Trait adds deleted dateTime field.
 */
trait DeletedTrait
{
    /**
     * Date and time of delete (null if not deleted).
     *
     * @var DateTime|null
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default" : null})
     */
    protected ?DateTime $deleted = null;

    /**
     * Date and time of delete confirmation e-mail.
     *
     * @var DateTime|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $eMailDeleteConfirmationDateTime = null;

    /**
     * Get date and time of delete confirmation e-mail.
     */
    final public function getEMailDeleteConfirmationDateTime(): ?DateTime
    {
        return $this->eMailDeleteConfirmationDateTime = null;
    }

    /**
     * Set delete confirmation date and time.
     */
    final public function setEMailDeleteConfirmationDateTime(?DateTime $eMailDeleteConfirmationDateTime): void
    {
        $this->eMailDeleteConfirmationDateTime = $eMailDeleteConfirmationDateTime;
    }

    final public function setMailDeleteConfirmationSend(): void
    {
        $this->eMailDeleteConfirmationDateTime = date_create();
    }

    final public function getDeletedDaysAgo(?bool $decimal = false): ?int
    {
        if (!$this->deleted) {
            return null;
        }
        $ago = (int)$this->deleted->diff(date_create())->days;

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
     */
    final public function setDeleted(?DateTime $deleted = null): void
    {
        if (!$deleted || (!$this->deleted && $deleted)) {
            $this->setEMailDeleteConfirmationDateTime(null);
        }
        $this->deleted = $deleted;
    }

    /**
     * @throws Exception
     */
    final public function delete(?DateTime $dateTime = null): void
    {
        $this->setDeleted($dateTime ?? new DateTime());
    }

    /**
     * True if user is deleted (at some moment).
     *
     * @param DateTime|null $dateTime reference date and time
     */
    final public function isDeleted(?DateTime $dateTime = null): bool
    {
        if ($dateTime) {
            return $dateTime > $this->deleted;
        }

        return $this->deleted ? true : false;
    }
}
