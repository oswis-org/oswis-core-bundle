<?php /** @noinspection PhpUnused */

/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait IdentificationNumberContainerTrait
{
    final public function setIdentificationNumber(?string $identificationNumber): void
    {
        if ($this->getIdentificationNumber() !== $identificationNumber) {
            $newRevision = clone $this->getRevision();
            $newRevision->setIdentificationNumber($identificationNumber);
            $this->addRevision($newRevision);
        }
    }

    final public function getIdentificationNumber(?DateTime $dateTime = null): ?string
    {
        if ($this->getRevisionByDate($dateTime)->getIdentificationNumber()) {
            return $this->getRevisionByDate($dateTime)->getIdentificationNumber();
        }

        return null;
    }
}
