<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Payment;

trait BankAccountTrait
{
    /**
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $bankAccountNumber = null;

    /**
     * Second line of street.
     *
     * @var string|null Second line of street
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $bankAccountBank = null;

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

    /**
     * @return string
     */
    public function getBankAccountComplete(): ?string
    {
        $fullBankAccount = $this->bankAccountNumber;
        if ($this->bankAccountNumber && $this->bankAccountBank) {
            $fullBankAccount .= '/'.$this->bankAccountBank;
        }

        return $fullBankAccount;
    }

}
