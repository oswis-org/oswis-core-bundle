<?php

/**
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
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

/**
 * Trait adds "active" field.
 */
trait ActivatedTrait
{
    /** Active after date-time. */
    #[Column(type: 'datetime', nullable: true, options: ['default' => null])]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(DateFilter::class)]
    #[ApiFilter(ExistsFilter::class)]
    protected ?DateTime $activated = null;

    public function isActivated(?DateTime $dateTime = null): bool
    {
        return null !== $this->activated && $this->activated <= ($dateTime ?? new DateTime());
    }

    public function activate(?DateTime $dateTime = null): void
    {
        $this->setActivated($this->getActivated() ?? $dateTime ?? new DateTime());
    }

    public function getActivated(): ?DateTime
    {
        return $this->activated;
    }

    public function setActivated(?DateTime $activated): void
    {
        $this->activated = $activated;
    }
}
