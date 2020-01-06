<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUndefinedMethodInspection
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

/**
 * Trait adds getters and setters for container of entity with age range fields.
 */
trait DateTimeContainerTrait
{
    public function setDateTime(?DateTime $dateTime): void
    {
        if ($this->getDateTime() !== $dateTime) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setDateTime($dateTime);
            $this->addRevision($newRevision);
        }
    }

    public function getDateTime(?DateTime $dateTime = null): ?DateTime
    {
        return $this->getRevisionByDate($dateTime)->getDateTime();
    }
}
