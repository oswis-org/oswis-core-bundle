<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use DateTime;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;

/**
 * Trait UserConfirmationTrait.
 */
trait UserConfirmationTrait
{
    /**
     * Date and time of user confirmation.
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?DateTime $userConfirmAt = null;

    /**
     * User who confirmed.
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(name="app_user_id", referencedColumnName="id")
     */
    protected ?AppUser $userConfirmBy = null;

    public function getUserConfirmBy(): ?AppUser
    {
        return $this->userConfirmBy;
    }

    public function setUserConfirmBy(?AppUser $appUser): void
    {
        $this->userConfirmBy = $appUser;
    }

    public function isConfirmedByUser(): bool
    {
        return $this->userConfirmAt ? true : false;
    }

    public function getUserConfirmAt(): ?DateTime
    {
        return $this->userConfirmAt;
    }

    public function setUserConfirmAt(?DateTime $dateTime): void
    {
        $this->userConfirmAt = $this->$dateTime;
    }

    public function confirmByUser(AppUser $appUser): void
    {
        if (!$this->userConfirmAt) {
            $this->setUserConfirmAt(new DateTime());
            $this->setUserConfirmBy($appUser);

        }
    }
}
