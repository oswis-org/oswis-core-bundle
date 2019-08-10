<?php
/** @noinspection PhpDocRedundantThrowsInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Zakjakub\OswisCoreBundle\Exceptions\RevisionMissingException;

/**
 * Trait adds getters and setters for container of entity with address fields.
 */
trait BankAccountContainerTrait
{

    /**
     * @param string|null $bankAccountNumber
     *
     * @throws RevisionMissingException
     */
    final public function setBankAccountNumber(?string $bankAccountNumber): void
    {
        if ($this->getBankAccountNumber() !== $bankAccountNumber) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setBankAccountNumber($bankAccountNumber);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @param DateTime|null $dateTime
     *
     * @return string|null
     * @throws RevisionMissingException
     */
    final public function getBankAccountNumber(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getBankAccountNumber();
    }

    /**
     * @param string|null $bankAccountBank
     *
     * @throws RevisionMissingException
     */
    final public function setBankAccountBank(?string $bankAccountBank): void
    {
        if ($this->getBankAccountBank() !== $bankAccountBank) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setBankAccountBank($bankAccountBank);
            $this->addRevision($newRevision);
        }
    }

    /**
     * @param DateTime|null $dateTime
     *
     * @return int|null
     * @throws RevisionMissingException
     */
    final public function getBankAccountBank(?DateTime $dateTime = null): ?int
    {
        return $this->getRevisionByDate($dateTime)->getBankAccountBank();
    }

    final public function getBankAccountComplete(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getBankAccountComplete();
    }
}
