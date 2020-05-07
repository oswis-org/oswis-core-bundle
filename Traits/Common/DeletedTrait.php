<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

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

    public function getDeletedDaysAgo(): ?int
    {
        return $this->deleted ? $this->deleted->diff(new DateTime())->d : null;
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

    public function setDeleted(?DateTime $deleted = null): void
    {
        if (null === $deleted || (null === $this->deleted && null !== $deleted)) {
            $this->setEMailDeleteConfirmationDateTime(null);
        }
        $this->deleted = $deleted;
    }

    public function isDeleted(?DateTime $referenceDateTime = null): bool
    {
        try {
            return $this->deleted && ($referenceDateTime ?? new DateTime()) > $this->deleted;
        } catch (Exception $e) {
            return false;
        }
    }
}
