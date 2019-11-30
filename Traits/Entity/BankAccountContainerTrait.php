<?php /** @noinspection PhpUnused */

/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

/**
 * Trait adds getters and setters for container of entity with address fields.
 */
trait BankAccountContainerTrait
{
    final public function setBankAccountNumber(?string $bankAccountNumber): void
    {
        if ($this->getBankAccountNumber() !== $bankAccountNumber) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setBankAccountNumber($bankAccountNumber);
            $this->addRevision($newRevision);
        }
    }

    final public function getBankAccountNumber(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getBankAccountNumber();
    }

    final public function setBankAccountBank(?string $bankAccountBank): void
    {
        if ($this->getBankAccountBank() !== $bankAccountBank) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setBankAccountBank($bankAccountBank);
            $this->addRevision($newRevision);
        }
    }

    final public function getBankAccountBank(?DateTime $dateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getBankAccountBank();
    }

    final public function getBankAccountComplete(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getBankAccountComplete();
    }
}
