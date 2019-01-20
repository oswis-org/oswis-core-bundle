<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Zakjakub\OswisCoreBundle\Utils\StringUtils;

/**
 * Trait adds fields for password reset.
 */
trait PasswordResetTrait
{

    /**
     * Token for password reset.
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected $passwordResetRequestToken;

    /**
     * Date and time of password reset request (and token generation).
     * @var \DateTime|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected $passwordResetRequestDateTime;

    final public function checkAndDestroyPasswordResetRequestToken(?string $token, int $validHours = 12): bool
    {
        try {
            $diff = (new \DateTime())->diff($this->getPasswordResetRequestDateTime());
            if ($validHours < $diff->h) {
                $this->destroyPasswordResetRequestToken();

                return false;
            }
            if ($this->checkPasswordResetRequestToken($token)) {
                $this->destroyPasswordResetRequestToken();

                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * @return \DateTime|null
     */
    final public function getPasswordResetRequestDateTime(): ?\DateTime
    {
        return $this->passwordResetRequestDateTime;
    }

    /**
     * @param \DateTime|null $passwordResetRequestDateTime
     */
    final public function setPasswordResetRequestDateTime(?\DateTime $passwordResetRequestDateTime): void
    {
        $this->passwordResetRequestDateTime = $passwordResetRequestDateTime;
    }

    final public function destroyPasswordResetRequestToken(): void
    {
        $this->setPasswordResetRequestDateTime(null);
        $this->setPasswordResetRequestToken(null);
    }

    final public function checkPasswordResetRequestToken(?string $token): bool
    {
        return $token && $token === $this->getPasswordResetRequestToken();
    }

    /**
     * @return string|null
     */
    final public function getPasswordResetRequestToken(): ?string
    {
        return $this->passwordResetRequestToken;
    }

    /**
     * @param string|null $passwordResetRequestToken
     */
    final public function setPasswordResetRequestToken(?string $passwordResetRequestToken): void
    {
        $this->passwordResetRequestToken = $passwordResetRequestToken;
    }

    final public function generatePasswordRequestToken(): ?string
    {
        try {
            $this->setPasswordResetRequestToken(StringUtils::generateToken());
            $this->setPasswordResetRequestDateTime(new \DateTime());

            return $this->getPasswordResetRequestToken();
        } catch (\Exception $e) {
            $this->destroyPasswordResetRequestToken();

            return null;
        }
    }
}
