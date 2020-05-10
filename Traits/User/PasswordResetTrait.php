<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\User;

use DateTime;
use Exception;
use OswisOrg\OswisCoreBundle\Utils\StringUtils;

/**
 * Trait adds fields for password reset.
 */
trait PasswordResetTrait
{
    /**
     * Token for password reset.
     *
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true, unique=true, length=100)
     */
    protected ?string $passwordResetRequestToken = null;

    /**
     * Date and time of password reset request (and token generation).
     *
     * @var DateTime|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $passwordResetRequestDateTime = null;

    public function checkAndDestroyPasswordResetRequestToken(?string $token, int $validHours = 24): bool
    {
        try {
            $diff = (new DateTime())->diff($this->getPasswordResetRequestDateTime());
            if (empty($diff) || $validHours < $diff->h) {
                $this->destroyPasswordResetRequestToken();

                return false;
            }
            if ($this->checkPasswordResetRequestToken($token)) {
                $this->destroyPasswordResetRequestToken();

                return true;
            }
        } catch (Exception $e) {
        }

        return false;
    }

    public function getPasswordResetRequestDateTime(): ?DateTime
    {
        return $this->passwordResetRequestDateTime;
    }

    public function setPasswordResetRequestDateTime(?DateTime $passwordResetRequestDateTime): void
    {
        $this->passwordResetRequestDateTime = $passwordResetRequestDateTime;
    }

    public function destroyPasswordResetRequestToken(): void
    {
        $this->setPasswordResetRequestDateTime(null);
        $this->setPasswordResetRequestToken(null);
    }

    public function checkPasswordResetRequestToken(?string $token): bool
    {
        return $token && $token === $this->getPasswordResetRequestToken();
    }

    public function getPasswordResetRequestToken(): ?string
    {
        return $this->passwordResetRequestToken;
    }

    public function setPasswordResetRequestToken(?string $passwordResetRequestToken): void
    {
        $this->passwordResetRequestToken = $passwordResetRequestToken;
    }

    public function generatePasswordRequestToken(): ?string
    {
        try {
            $this->setPasswordResetRequestToken(StringUtils::generateToken());
            $this->setPasswordResetRequestDateTime(new DateTime());

            return $this->getPasswordResetRequestToken();
        } catch (Exception $e) {
            $this->destroyPasswordResetRequestToken();

            return null;
        }
    }
}
