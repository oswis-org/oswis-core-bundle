<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

use rikudou\CzQrPayment\QrPayment;
use rikudou\CzQrPayment\QrPaymentException;
use rikudou\CzQrPayment\QrPaymentOptions;

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
            $qrPayment = $this->getQrPayment($value, $variableSymbol, $comment);

            /** @noinspection PhpUndefinedMethodInspection */
            return $qrPayment ? $qrPayment->getQrImage(true)->writeString() : null;
        } catch (QrPaymentException $e) {
            return null;
        }
    }

    private function getQrPayment(?int $value = 0, ?string $variableSymbol = '', ?string $comment = ''): ?QrPayment
    {
        try {
            return new QrPayment(
                $this->getAccountWithoutBankCode(), $this->getBankCode(), [
                    QrPaymentOptions::VARIABLE_SYMBOL => $variableSymbol,
                    QrPaymentOptions::AMOUNT          => $value,
                    QrPaymentOptions::COMMENT         => $comment,
                ]
            );
        } catch (QrPaymentException $e) {
            return null;
        }
    }

    public function getAccountWithoutBankCode(): ?string
    {
        return (empty($this->getPrefix()) ? null : $this->getPrefix()).$this->getAccountNumber();
    }

}
