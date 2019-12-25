<?php /** @noinspection MethodShouldBeFinalInspection */

/** @noinspection PhpUnused */

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
        $this->eMailDeleteConfirmationDateTime = date_create();
    }

    public function getDeletedDaysAgo(?bool $decimal = false): ?int
    {
        if (!$this->deleted) {
            return null;
        }
        $ago = (int)$this->deleted->diff(date_create())->days;

        return $decimal ? $ago : floor($ago);
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
     * True if user is deleted (at some moment).
     *
     * @param DateTime|null $dateTime Reference date and time.
     */
    public function isDeleted(?DateTime $dateTime = null): bool
    {
        try {
            return ($dateTime ?? new DateTime()) > $this->deleted;
        } catch (Exception $e) {
            return false;
        }
    }
}
