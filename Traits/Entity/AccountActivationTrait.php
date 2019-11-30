<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use Exception;
use Zakjakub\OswisCoreBundle\Utils\StringUtils;

/**
 * Trait adds fields for account activation.
 */
trait AccountActivationTrait
{
    /**
     * Token for password reset.
     *
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true, unique=true, length=100)
     */
    protected ?string $accountActivationRequestToken = null;

    /**
     * Date and time of password reset request (and token generation).
     *
     * @var DateTime|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $accountActivationRequestDateTime = null;

    /**
     * Date and time of account activation.
     *
     * @var DateTime|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $accountActivationDateTime = null;

    final public function checkAndDestroyAccountActivationRequestToken(?string $token, int $validHours = 24): bool
    {
        $diff = null;
        if ($this->getAccountActivationRequestDateTime()) {
            $diff = $this->getAccountActivationRequestDateTime()->diff(new DateTime());
        }
        if ($diff && $validHours < $diff->h) {
            $this->destroyAccountActivationRequestToken();

            return false;
        }
        if ($diff && $this->checkAccountActivationRequestToken($token)) {
            $this->destroyAccountActivationRequestToken();
            $this->setAccountActivationDateTime(new DateTime());

            return true;
        }

        return false;
    }

    final public function getAccountActivationRequestDateTime(): ?DateTime
    {
        return $this->accountActivationRequestDateTime;
    }

    final public function setAccountActivationRequestDateTime(?DateTime $accountActivationRequestDateTime): void
    {
        $this->accountActivationRequestDateTime = $accountActivationRequestDateTime;
    }

    final public function destroyAccountActivationRequestToken(): void
    {
        $this->setAccountActivationRequestDateTime(null);
        $this->setAccountActivationRequestToken(null);
    }

    final public function checkAccountActivationRequestToken(?string $token): bool
    {
        return $token && $token === $this->getAccountActivationRequestToken();
    }

    final public function getAccountActivationRequestToken(): ?string
    {
        return $this->accountActivationRequestToken;
    }

    final public function setAccountActivationRequestToken(?string $accountActivationRequestToken): void
    {
        $this->accountActivationRequestToken = $accountActivationRequestToken;
    }

    final public function generateAccountActivationRequestToken(): ?string
    {
        try {
            $this->setAccountActivationRequestToken(StringUtils::generateToken());
            $this->setAccountActivationRequestDateTime(new DateTime());

            return $this->getAccountActivationRequestToken();
        } catch (Exception $e) {
            $this->destroyAccountActivationRequestToken();

            return null;
        }
    }

    final public function getAccountActivationDateTime(): ?DateTime
    {
        return $this->accountActivationDateTime;
    }

    final public function setAccountActivationDateTime(?DateTime $accountActivationDateTime): void
    {
        $this->accountActivationDateTime = $accountActivationDateTime;
    }
}
