<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

/**
 * Trait ManagerConfirmationTrait.
 */
trait ManagerConfirmationTrait
{
    /** Date and time of manager confirmation. */
    #[Column(type: 'datetime', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(DateFilter::class)]
    #[ApiFilter(ExistsFilter::class)]
    protected ?DateTime $managerConfirmedAt = null;

    /** Manager who confirmed. */
    #[ManyToOne(targetEntity: AppUser::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'manager_confirmed_by_id', referencedColumnName: 'id')]
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
