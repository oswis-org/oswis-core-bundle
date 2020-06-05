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
    protected ?string $bankAccountNumber = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $bankAccountBank = null;

    public function getBankAccount(): BankAccount
    {
        return new BankAccount($this->getBankAccountNumber(), $this->getBankAccountBank());
    }

    public function setBankAccount(?BankAccount $bankAccount): void
    {
        $this->setBankAccountNumber($bankAccount ? $bankAccount->getBankAccountNumber() : null);
        $this->setBankAccountBank($bankAccount ? $bankAccount->getBankAccountBank() : null);
    }

    public function getBankAccountNumber(): ?string
    {
        return $this->bankAccountNumber;
    }

    public function setBankAccountNumber(?string $bankAccountNumber): void
    {
        $this->bankAccountNumber = $bankAccountNumber;
    }

    public function getBankAccountBank(): ?string
    {
        return $this->bankAccountBank;
    }

    public function setBankAccountBank(?string $bankAccountBank): void
    {
        $this->bankAccountBank = $bankAccountBank;
    }

    public function getFullBankAccount(): ?string
    {
        return $this->getBankAccountComplete();
    }

    public function getBankAccountComplete(): ?string
    {
        $fullBankAccount = $this->bankAccountNumber;
        if ($this->bankAccountNumber && $this->bankAccountBank) {
            $fullBankAccount .= '/'.$this->bankAccountBank;
        }

        return $fullBankAccount;
    }
}
