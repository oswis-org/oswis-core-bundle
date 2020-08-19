<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use DateTime;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;

/**
 * Trait ManagerConfirmationTrait.
 */
trait ManagerConfirmationTrait
{
    /**
     * Date and time of manager confirmation.
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?DateTime $managerConfirmedAt = null;

    /**
     * Manager who confirmed.
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(name="manager_confirmed_by_id", referencedColumnName="id")
     */
    protected ?AppUser $managerConfirmedBy = null;

    public function getManagerConfirmedBy(): ?AppUser
    {
        return $this->managerConfirmedBy;
    }

    public function setManagerConfirmedBy(?AppUser $appUser): void
    {
        $this->managerConfirmedBy = $appUser;
    }

    public function getManagerConfirmedAt(): ?DateTime
    {
        return $this->managerConfirmedAt;
    }

    public function setManagerConfirmedAt(?DateTime $dateTime): void
    {
        $this->managerConfirmedAt = $dateTime;
    }

    public function setManagerConfirmed(AppUser $appUser): void
    {
        if (!$this->managerConfirmedAt) {
            $this->setManagerConfirmedAt(new DateTime());
            $this->setManagerConfirmedBy($appUser);

        }
    }
}
