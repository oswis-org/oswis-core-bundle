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
 * Trait adds fields for account activation.
 */
trait AccountActivationTrait
{
    /**
     * Token for password reset.
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true, unique=true, length=100)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     */
    protected ?string $activationRequestToken = null;

    /**
     * Date and time of password reset request (and token generation).
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?DateTime $activationRequestDateTime = null;

    /**
     * Date and time of account activation.
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?DateTime $activationDateTime = null;

    public function activateByToken(?string $token, int $validHours = 24): bool
    {
        if (empty($token) || $this->isAccountActivated() || null === $this->getActivationRequestDateTime()) {
            return false;
        }
        $diff = $this->getActivationRequestDateTime()->diff(new DateTime())->h;
        if ($diff > $validHours) {
            $this->destroyActivationRequestToken();

            return false;
        }
        if ($this->checkActivationRequestToken($token)) {
            $this->destroyActivationRequestToken();
            $this->setActivationDateTime(new DateTime());

            return true;
        }

        return false;
    }

    public function isAccountActivated(): bool
    {
        return null !== $this->getActivationDateTime();
    }

    public function getActivationDateTime(): ?DateTime
    {
        return $this->activationDateTime;
    }

    public function setActivationDateTime(?DateTime $activationDateTime): void
    {
        $this->activationDateTime = $activationDateTime;
    }

    public function getActivationRequestDateTime(): ?DateTime
    {
        return $this->activationRequestDateTime;
    }

    public function setActivationRequestDateTime(?DateTime $activationRequestDateTime): void
    {
        $this->activationRequestDateTime = $activationRequestDateTime;
    }

    public function destroyActivationRequestToken(): void
    {
        $this->setActivationRequestDateTime(null);
        $this->setActivationRequestToken(null);
    }

    public function checkActivationRequestToken(?string $token): bool
    {
        return !empty($token) && !empty($this->getActivationRequestToken()) && $token === $this->getActivationRequestToken();
    }

    public function getActivationRequestToken(): ?string
    {
        return $this->activationRequestToken;
    }

    public function setActivationRequestToken(?string $activationRequestToken): void
    {
        $this->activationRequestToken = $activationRequestToken;
    }

    public function generateActivationRequestToken(): ?string
    {
        try {
            $this->setActivationRequestToken(StringUtils::generateToken());
            $this->setActivationRequestDateTime(new DateTime());

            return $this->getActivationRequestToken();
        } catch (Exception $e) {
            $this->destroyActivationRequestToken();

            return null;
        }
    }
}
