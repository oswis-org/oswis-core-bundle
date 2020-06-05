<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Payment;

use OswisOrg\OswisCoreBundle\Entity\NonPersistent\BankAccount;

trait BankAccountTrait
{
    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $bankAccountPrefix = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $bankAccountNumber = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $bankAccountBank = null;

    public function getBankAccount(): BankAccount
    {
        return new BankAccount($this->bankAccountPrefix, $this->bankAccountNumber, $this->bankAccountBank);
    }

    public function setBankAccount(?BankAccount $bankAccount): void
    {
        $this->bankAccountPrefix = $bankAccount ? $bankAccount->getPrefix() : null;
        $this->bankAccountNumber = $bankAccount ? $bankAccount->getAccountNumber() : null;
        $this->bankAccountBank = $bankAccount ? $bankAccount->getBankCode() : null;
    }

    public function getBankAccountNumber(): ?string
    {
        return $this->getBankAccount()->getAccountNumber();
    }

    public function setBankAccountNumber(?string $bankAccountNumber): void
    {
        $this->bankAccountNumber = $bankAccountNumber;
    }

    public function getBankAccountBank(): ?string
    {
        return $this->getBankAccount()->getBankCode();
    }

    public function setBankAccountBank(?string $bankAccountBank): void
    {
        $this->bankAccountBank = $bankAccountBank;
    }

    public function getBankAccountPrefix(): ?string
    {
        return $this->getBankAccount()->getPrefix();
    }

    public function getBankAccountFull(): ?string
    {
        return $this->getBankAccount()->getFull();
    }

    public function setBankAccountPrefix(?string $bankAccountPrefix): void
    {
        $this->bankAccountPrefix = $bankAccountPrefix;
    }
}
