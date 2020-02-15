<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use DateTimeInterface;
use Exception;
use Zakjakub\OswisCoreBundle\Utils\StringUtils;

/**
 * Trait adds fields for account activation.
 */
trait AccountActivationTrait
{
    /**
     * Token for password reset.
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true, unique=true, length=100)
     */
    protected ?string $accountActivationRequestToken = null;

    /**
     * Date and time of password reset request (and token generation).
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTimeInterface $accountActivationRequestDateTime = null;

    /**
     * Date and time of account activation.
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTimeInterface $accountActivationDateTime = null;

    public function checkAndDestroyAccountActivationRequestToken(?string $token, int $validHours = 24): bool
    {
        $diff = null;
        if ($this->getAccountActivationRequestDateTime()) {
            $diff = $this->getAccountActivationRequestDateTime()->diff(new DateTime());
        }
        if ($diff && ($validHours < $diff->h)) {
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

    public function getAccountActivationRequestDateTime(): ?DateTimeInterface
    {
        return $this->accountActivationRequestDateTime;
    }

    public function setAccountActivationRequestDateTime(?DateTimeInterface $accountActivationRequestDateTime): void
    {
        $this->accountActivationRequestDateTime = $accountActivationRequestDateTime;
    }

    public function destroyAccountActivationRequestToken(): void
    {
        $this->setAccountActivationRequestDateTime(null);
        $this->setAccountActivationRequestToken(null);
    }

    public function checkAccountActivationRequestToken(?string $token): bool
    {
        return $token && $token === $this->getAccountActivationRequestToken();
    }

    public function getAccountActivationRequestToken(): ?string
    {
        return $this->accountActivationRequestToken;
    }

    public function setAccountActivationRequestToken(?string $accountActivationRequestToken): void
    {
        $this->accountActivationRequestToken = $accountActivationRequestToken;
    }

    public function generateAccountActivationRequestToken(): ?string
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

    public function getAccountActivationDateTime(): ?DateTimeInterface
    {
        return $this->accountActivationDateTime;
    }

    public function setAccountActivationDateTime(?DateTimeInterface $accountActivationDateTime): void
    {
        $this->accountActivationDateTime = $accountActivationDateTime;
    }
}
