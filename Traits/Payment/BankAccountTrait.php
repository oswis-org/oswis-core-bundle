<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Payment;

use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\BankAccount;

trait BankAccountTrait
{
    #[Column(type: 'string', nullable: true)]
    protected ?string $bankAccountPrefix = null;

    #[Column(type: 'string', nullable: true)]
    protected ?string $bankAccountNumber = null;

    #[Column(type: 'string', nullable: true)]
    protected ?string $bankAccountBank = null;

    public function setBankAccount(?BankAccount $bankAccount): void
    {
        $this->bankAccountPrefix = $bankAccount?->getPrefix();
        $this->bankAccountNumber = $bankAccount?->getAccountNumber();
        $this->bankAccountBank = $bankAccount?->getBankCode();
    }

    public function getBankAccountNumber(): ?string
    {
        return $this->getBankAccount() ? $this->getBankAccount()->getAccountNumber() : null;
    }

    public function setBankAccountNumber(?string $bankAccountNumber): void
    {
        $this->bankAccountNumber = $bankAccountNumber;
    }

    public function getBankAccount(): BankAccount
    {
        return new BankAccount($this->bankAccountPrefix, $this->bankAccountNumber, $this->bankAccountBank);
    }

    public function getBankAccountBank(): ?string
    {
        return $this->getBankAccount() ? $this->getBankAccount()->getBankCode() : null;
    }

    public function setBankAccountBank(?string $bankAccountBank): void
    {
        $this->bankAccountBank = $bankAccountBank;
    }

    public function getBankAccountPrefix(): ?string
    {
        return $this->getBankAccount() ? $this->getBankAccount()->getPrefix() : null;
    }

    public function setBankAccountPrefix(?string $bankAccountPrefix): void
    {
        $this->bankAccountPrefix = $bankAccountPrefix;
    }

    public function getBankAccountFull(): ?string
    {
        return $this->getBankAccount() ? $this->getBankAccount()->getFull() : null;
    }
}
