<?php
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait IdentificationNumberContainerTrait
{

    /**
     * @param string|null $identificationNumber
     *
     * @throws RevisionMissingException
     */
    final public function setIdentificationNumber(?string $identificationNumber): void
    {
        if ($this->getIdentificationNumber() !== $identificationNumber) {
            $newRevision = clone $this->getRevision();
            $newRevision->setIdentificationNumber($identificationNumber);
            $this->addRevision($newRevision);
        }
    }

    final public function getIdentificationNumber(?\DateTime $dateTime = null): ?string
    {
        if ($this->getRevisionByDate($dateTime)->getIdentificationNumber()) {
            return $this->getRevisionByDate($dateTime)->getIdentificationNumber();
        }
        try {
            return $this->getIdentificationNumberFromParents();
        } catch (\Exception $exception) {
            return null;
        }
    }

}
