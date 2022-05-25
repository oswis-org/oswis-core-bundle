<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

trait UserConfirmationTrait
{
    /** Date and time of user confirmation. */
    #[Column(type: 'datetime', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(DateFilter::class)]
    #[ApiFilter(ExistsFilter::class)]
    #[ApiFilter(OrderFilter::class)]
    protected ?DateTime $userConfirmedAt = null;

    /** User who confirmed. */
    #[ManyToOne(targetEntity: AppUser::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'user_confirmed_by_id', referencedColumnName: 'id')]
    protected ?AppUser $userConfirmedBy = null;

    public function getUserConfirmedBy(): ?AppUser
    {
        return $this->userConfirmedBy;
    }

    public function setUserConfirmedBy(?AppUser $appUser): void
    {
        $this->userConfirmedBy = $appUser;
    }

    public function getUserConfirmedAt(): ?DateTime
    {
        return $this->userConfirmedAt;
    }

    public function setUserConfirmedAt(?DateTime $dateTime): void
    {
        $this->userConfirmedAt = $dateTime;
    }

    public function setUserConfirmed(AppUser $appUser): void
    {
        if (!$this->userConfirmedAt) {
            $this->setUserConfirmedAt(new DateTime());
            $this->setUserConfirmedBy($appUser);
        }
    }
}
