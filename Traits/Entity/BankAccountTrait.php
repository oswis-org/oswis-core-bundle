<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

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

    final public function getBankAccountNumber(): ?string
    {
        return $this->bankAccountNumber;
    }

    final public function setBankAccountNumber(?string $bankAccountNumber): void
    {
        $this->bankAccountNumber = $bankAccountNumber;
    }

    final public function getBankAccountBank(): ?string
    {
        return $this->bankAccountBank;
    }

    final public function setBankAccountBank(?string $bankAccountBank): void
    {
        $this->bankAccountBank = $bankAccountBank;
    }

    final public function getFullBankAccount(): ?string
    {
        return $this->getBankAccountComplete();
    }

    /**
     * @return string
     */
    final public function getBankAccountComplete(): ?string
    {
        $fullBankAccount = $this->bankAccountNumber;
        if ($this->bankAccountNumber && $this->bankAccountBank) {
            $fullBankAccount .= '/'.$this->bankAccountBank;
        }

        return $fullBankAccount;
    }

}
