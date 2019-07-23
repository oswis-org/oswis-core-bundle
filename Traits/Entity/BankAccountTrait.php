<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait BankAccountTrait
{

    /**
     * @var string|null $bankAccountNumber
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected $bankAccountNumber;

    /**
     * Second line of street
     *
     * @var string|null $bankAccountBank Second line of street
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected $bankAccountBank;

    /**
     * @return string|null
     */
    final public function getBankAccountNumber(): ?string
    {
        return $this->bankAccountNumber ?? '';
    }

    /**
     * @param string|null $bankAccountNumber
     */
    final public function setBankAccountNumber(?string $bankAccountNumber): void
    {
        $this->bankAccountNumber = $bankAccountNumber;
    }

    /**
     * @return string|null
     */
    final public function getBankAccountBank(): ?string
    {
        return $this->bankAccountBank ?? '';
    }

    /**
     * @param string|null $bankAccountBank
     */
    final public function setBankAccountBank(?string $bankAccountBank): void
    {
        $this->bankAccountBank = $bankAccountBank;
    }

    /**
     * @return string
     */
    final public function getBankAccountComplete(): ?string
    {
        $fullBankAccount = $this->bankAccountNumber;
        if ($this->bankAccountNumber && $this->bankAccountBank) {
            $fullBankAccount .= '/'.$this->bankAccountNumber;
        }

        return $fullBankAccount;
    }

}
