<?php /** @noinspection PhpUnused */

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
     * Date and time of delete (null if not deleted).
     *
     * @var DateTime|null
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default" : null})
     */
    protected $deleted;

    /**
     * Date and time of delete confirmation e-mail.
     * @var DateTime|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected $eMailDeleteConfirmationDateTime;

    /**
     * Get date and time of delete confirmation e-mail.
     * @return DateTime|null
     */
    final public function getEMailDeleteConfirmationDateTime(): ?DateTime
    {
        return $this->eMailDeleteConfirmationDateTime;
    }

    /**
     * Set delete confirmation date and time.
     *
     * @param DateTime|null $eMailDeleteConfirmationDateTime
     */
    final public function setEMailDeleteConfirmationDateTime(?DateTime $eMailDeleteConfirmationDateTime): void
    {
        $this->eMailDeleteConfirmationDateTime = $eMailDeleteConfirmationDateTime;
    }

    final public function setMailDeleteConfirmationSend(): void
    {
        $this->eMailDeleteConfirmationDateTime = date_create();
    }

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
        if (!$deleted || (!$this->deleted && $deleted)) {
            $this->setEMailDeleteConfirmationDateTime(null);
        }
        $this->deleted = $deleted;
    }

    /**
     * @param DateTime|null $dateTime
     *
     * @throws Exception
     */
    final public function delete(?DateTime $dateTime = null): void
    {
        $this->setDeleted($dateTime ?? new DateTime());
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
