<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use DateTimeInterface;
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
    protected ?DateTimeInterface $deleted = null;

    /**
     * Date and time of delete confirmation e-mail.
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTimeInterface $eMailDeleteConfirmationDateTime = null;

    /**
     * Get date and time of delete confirmation e-mail.
     */
    public function getEMailDeleteConfirmationDateTime(): ?DateTimeInterface
    {
        return $this->eMailDeleteConfirmationDateTime = null;
    }

    /**
     * Set delete confirmation date and time.
     */
    public function setEMailDeleteConfirmationDateTime(?DateTimeInterface $eMailDeleteConfirmationDateTime): void
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
    public function delete(?DateTimeInterface $dateTime = null): void
    {
        $this->setDeleted($this->getDeleted() ?? $dateTime ?? new DateTime());
    }

    /**
     * Get date and time when entity was deleted.
     */
    public function getDeleted(): ?DateTimeInterface
    {
        return $this->deleted;
    }

    public function setDeleted(?DateTimeInterface $deleted = null): void
    {
        if (null === $deleted || (null === $this->deleted && null !== $deleted)) {
            $this->setEMailDeleteConfirmationDateTime(null);
        }
        $this->deleted = $deleted;
    }

    public function isDeleted(?DateTimeInterface $referenceDateTime = null): bool
    {
        try {
            return $this->deleted && ($referenceDateTime ?? new DateTime()) > $this->deleted;
        } catch (Exception $e) {
            return false;
        }
    }
}
