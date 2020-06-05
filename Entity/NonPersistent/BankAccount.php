<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

class BankAccount
{
    protected ?string $prefix = null;

    protected ?string $accountNumber = null;

    protected ?string $bankCode = null;

    public function __construct(?string $prefix, ?string $bankAccountNumber, ?string $bankAccountBank)
    {
        $this->prefix = $prefix;
        $this->accountNumber = $bankAccountNumber;
        $this->bankCode = $bankAccountBank;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(?string $accountNumber): void
    {
        $this->accountNumber = $accountNumber;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(?string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    public function setBankCode(?string $bankCode): void
    {
        $this->bankCode = $bankCode;
    }

    public function getFull(): ?string
    {
        if (empty($this->getAccountNumber())) {
            return null;
        }
        $account = (empty($this->getPrefix()) ? '' : $this->getPrefix().'-').$this->getAccountNumber();
        $account .= empty($this->getBankCode()) ? null : ('/'.$this->getBankCode());

        return $account;
    }

}
