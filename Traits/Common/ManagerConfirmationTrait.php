<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
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
    protected ?DateTime $managerConfirmAt = null;

    /**
     * Manager who confirmed.
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(name="app_user_id", referencedColumnName="id")
     */
    protected ?AppUser $managerConfirmBy = null;

    public function getManagerConfirmBy(): ?AppUser
    {
        return $this->managerConfirmBy;
    }

    public function setManagerConfirmBy(?AppUser $appUser): void
    {
        $this->managerConfirmBy = $appUser;
    }

    public function isConfirmedByManager(): bool
    {
        return $this->managerConfirmAt ? true : false;
    }

    public function getManagerConfirmAt(): ?DateTime
    {
        return $this->managerConfirmAt;
    }

    public function setManagerConfirmAt(?DateTime $dateTime): void
    {
        $this->managerConfirmAt = $this->$dateTime;
    }

    public function confirmByManager(AppUser $appUser): void
    {
        if (!$this->managerConfirmAt) {
            $this->setManagerConfirmAt(new DateTime());
            $this->setManagerConfirmBy($appUser);

        }
    }
}
