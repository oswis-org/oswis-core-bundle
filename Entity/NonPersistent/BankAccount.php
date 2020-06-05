<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

class BankAccount
{
    protected ?string $bankAccountNumber = null;

    protected ?string $bankAccountBank = null;

    public function __construct(?string $bankAccountNumber, ?string $bankAccountBank)
    {
        $this->bankAccountNumber = $bankAccountNumber;
        $this->bankAccountBank = $bankAccountBank;
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
}
