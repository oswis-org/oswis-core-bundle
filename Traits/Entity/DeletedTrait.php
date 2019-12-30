<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Exception;

/**
 * Trait adds deleted dateTime field.
 */
trait DeletedTrait
{
    /**
     * Date and time of delete (null if not deleted).
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default" : null})
     */
    protected ?DateTime $deleted = null;

    /**
     * Date and time of delete confirmation e-mail.
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $eMailDeleteConfirmationDateTime = null;

    /**
     * Get date and time of delete confirmation e-mail.
     */
    public function getEMailDeleteConfirmationDateTime(): ?DateTime
    {
        return $this->eMailDeleteConfirmationDateTime = null;
    }

    /**
     * Set delete confirmation date and time.
     */
    public function setEMailDeleteConfirmationDateTime(?DateTime $eMailDeleteConfirmationDateTime): void
    {
        $this->eMailDeleteConfirmationDateTime = $eMailDeleteConfirmationDateTime;
    }

    public function setMailDeleteConfirmationSend(): void
    {
        $this->eMailDeleteConfirmationDateTime = new DateTime();
    }

    public function getDeletedDaysAgo(?bool $decimal = false): ?float
    {
        if (null === $this->deleted) {
            return null;
        }
        $interval = $this->deleted->diff(new DateTime());

        return $decimal ? $interval->days : $interval->d;
    }

    /**
     * @throws Exception
     */
    public function delete(?DateTime $dateTime = null): void
    {
        $this->setDeleted($this->getDeleted() ?? $dateTime ?? new DateTime());
    }

    /**
     * Get date and time when entity was deleted.
     */
    public function getDeleted(): ?DateTime
    {
        return $this->deleted;
    }

    /**
     * Set date and time of delete.
     */
    public function setDeleted(?DateTime $deleted = null): void
    {
        if (null === $deleted || (null === $this->deleted && null !== $deleted)) {
            $this->setEMailDeleteConfirmationDateTime(null);
        }
        $this->deleted = $deleted;
    }

    /**
     * True if user is deleted.
     *
     * @param DateTime|null $referenceDateTime Reference date and time ('now' if not specified).
     */
    public function isDeleted(?DateTime $referenceDateTime = null): bool
    {
        try {
            $referenceDateTime ??= new DateTime();

            return ($referenceDateTime) > $this->deleted;
        } catch (Exception $e) {
            return false;
        }
    }
}
