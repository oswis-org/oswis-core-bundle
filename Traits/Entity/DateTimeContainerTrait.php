<?php /** @noinspection PhpUnused */
/** @noinspection PhpDocRedundantThrowsInspection */
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Zakjakub\OswisCoreBundle\Exceptions\RevisionMissingException;

/**
 * Trait adds getters and setters for container of entity with age range fields.
 */
trait DateTimeContainerTrait
{

    /**
     * @param DateTime|null $dateTime
     *
     * @throws RevisionMissingException
     */
    final public function setDateTime(?DateTime $dateTime): void
    {
        if ($this->getDateTime() != $dateTime) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setDateTime($dateTime);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @param DateTime|null $dateTime
     *
     * @return DateTime|null
     * @throws RevisionMissingException
     */
    final public function getDateTime(?DateTime $dateTime = null): ?DateTime
    {
        return $this->getRevisionByDate($dateTime)->getDateTime();
    }
}
