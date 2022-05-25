<?php
/**
 * @noinspection PhpUnused
 * @noinspection PropertyCanBePrivateInspection
 * @noinspection MethodShouldBeFinalInspection
 */

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

use Exception;
use InvalidArgumentException;
use Rikudou\CzQrPayment\Exception\QrPaymentException;
use Rikudou\CzQrPayment\Options\QrPaymentOptions;
use Rikudou\CzQrPayment\QrPayment;
use Rikudou\Iban\Iban\CzechIbanAdapter;

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

    public function getFull(): ?string
    {
        if (empty($this->getAccountNumber())) {
            return null;
        }
        $account = (empty($this->getPrefix()) ? '' : $this->getPrefix().'-').$this->getAccountNumber();
        $account .= empty($this->getBankCode()) ? null : ('/'.$this->getBankCode());

        return $account;
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

    public function getQrImage(?int $value = 0, ?string $variableSymbol = '', ?string $comment = ''): ?string
    {
        try {
            $qrCode = $this->getQrPayment($value, $variableSymbol, $comment);
            if ($qrCode) {
                return $qrCode->getQrCode()->getRawString();
            }
        } catch (Exception) {
            return null;
        }

        return null;
    }

    private function getQrPayment(?int $value = 0, ?string $variableSymbol = '', ?string $comment = ''): ?QrPayment
    {
        try {
            return new QrPayment(new CzechIbanAdapter(''.$this->getAccountWithoutBankCode(), ''.$this->getBankCode()), [
                QrPaymentOptions::VARIABLE_SYMBOL => $variableSymbol,
                QrPaymentOptions::AMOUNT          => $value,
                QrPaymentOptions::COMMENT         => $comment,
            ],);
        } catch (InvalidArgumentException|QrPaymentException) {
            return null;
        }
    }

    public function getAccountWithoutBankCode(): ?string
    {
        return (empty($this->getPrefix()) ? null : $this->getPrefix()).$this->getAccountNumber();
    }

}
