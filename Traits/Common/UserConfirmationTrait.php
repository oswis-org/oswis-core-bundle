<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use DateTime;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;

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
    protected ?DateTime $userConfirmedAt = null;

    /**
     * User who confirmed.
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(name="user_confirmed_by_id", referencedColumnName="id")
     */
    protected ?AppUser $userConfirmedBy = null;

    public function getUserConfirmedBy(): ?AppUser
    {
        return $this->userConfirmedBy;
    }

    public function setUserConfirmedBy(?AppUser $appUser): void
    {
        $this->userConfirmedBy = $appUser;
    }

    public function isUserConfirmed(): bool
    {
        return $this->userConfirmedAt ? true : false;
    }

    public function getUserConfirmedAt(): ?DateTime
    {
        return $this->userConfirmedAt;
    }

    public function setUserConfirmedAt(?DateTime $dateTime): void
    {
        $this->userConfirmedAt = $this->$dateTime;
    }

    public function setUserConfirmed(AppUser $appUser): void
    {
        if (!$this->userConfirmedAt) {
            $this->setUserConfirmedAt(new DateTime());
            $this->setUserConfirmedBy($appUser);

        }
    }
}
